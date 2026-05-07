<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\AdminSessionRepository;
use App\Repositories\FailedLoginAttemptRepository;
use App\Repositories\AdminActivityLogRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class AdminAuthService
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    protected AdminRepository $adminRepository;
    protected AdminSessionRepository $adminSessionRepository;
    protected FailedLoginAttemptRepository $failedLoginAttemptRepository;
    protected AdminActivityLogRepository $adminActivityLogRepository;

    public function __construct(
        AdminRepository $adminRepository,
        AdminSessionRepository $adminSessionRepository,
        FailedLoginAttemptRepository $failedLoginAttemptRepository,
        AdminActivityLogRepository $adminActivityLogRepository
    ) {
        $this->adminRepository = $adminRepository;
        $this->adminSessionRepository = $adminSessionRepository;
        $this->failedLoginAttemptRepository = $failedLoginAttemptRepository;
        $this->adminActivityLogRepository = $adminActivityLogRepository;
    }

    public function register(array $data)
    {
        return $this->adminRepository->save([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'moderator',
            'is_active' => true,
        ]);
    }

    public function login(array $credentials, string $ipAddress, ?string $userAgent)
    {
        $email = $credentials['email'];

        // 1. Check Rate Limiting
        $attempt = $this->checkRateLimit($email, $ipAddress);
        if (is_array($attempt) && isset($attempt['error'])) {
            return $attempt;
        }

        // 2. Validate Credentials
        $admin = $this->validateCredentials($email, $credentials['password'], $ipAddress, $attempt);
        if (is_array($admin) && isset($admin['error'])) {
            return $admin;
        }

        // 3. Handle 2FA
        $twoFactorResult = $this->verifyTwoFactor($admin, $credentials, $ipAddress, $attempt);
        if ($twoFactorResult) {
            return $twoFactorResult;
        }

        // 4. Finalize Login
        return $this->finalizeLogin($admin, $ipAddress, $userAgent, $attempt);
    }

    private function incrementFailedAttempt($email, $ipAddress, $attempt)
    {
        if (!$attempt) {
            $this->failedLoginAttemptRepository->save([
                'email' => $email,
                'ip_address' => $ipAddress,
                'attempts' => 1
            ]);
            return;
        }

        $attempts = $attempt->attempts + 1;
        $lockedUntil = $attempts >= self::MAX_ATTEMPTS ? now()->addMinutes(self::LOCKOUT_MINUTES) : null;

        $this->failedLoginAttemptRepository->update($attempt->id, [
            'attempts' => $attempts,
            'locked_until' => $lockedUntil
        ]);
    }

    /**
     * Checks if the user is currently rate-limited.
     */
    private function checkRateLimit(string $email, string $ipAddress)
    {
        $attempt = $this->failedLoginAttemptRepository->findByEmailAndIp($email, $ipAddress);

        if ($attempt && $attempt->locked_until && $attempt->locked_until > now()) {
            return [
                'error' => true,
                'status' => 429,
                'message' => 'Tài khoản tạm thời bị khóa do đăng nhập sai quá nhiều lần. Vui lòng thử lại sau.'
            ];
        }

        return $attempt;
    }

    /**
     * Validates user credentials and account status.
     */
    private function validateCredentials(string $email, string $password, string $ipAddress, $attempt)
    {
        $admin = $this->adminRepository->findByEmail($email);

        if (!$admin || !Hash::check($password, $admin->password_hash)) {
            $this->incrementFailedAttempt($email, $ipAddress, $attempt);
            return [
                'error' => true,
                'status' => 401,
                'message' => 'Thông tin đăng nhập không chính xác.'
            ];
        }

        if (!$admin->is_active) {
            return [
                'error' => true,
                'status' => 403,
                'message' => 'Tài khoản đã bị vô hiệu hóa.'
            ];
        }

        return $admin;
    }

    /**
     * Finalizes the login process after successful authentication and 2FA.
     */
    private function finalizeLogin($admin, string $ipAddress, ?string $userAgent, $attempt): array
    {
        // 1. Clear failed attempts
        if ($attempt) {
            $this->failedLoginAttemptRepository->delete($attempt->id);
        }

        // 2. Update admin info
        $this->adminRepository->update($admin->id, [
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress
        ]);

        // 3. Generate Auth Tokens
        $token = auth('admin')->login($admin);
        $rawRefreshToken = Str::random(60);

        // 4. Create Session Record
        $this->adminSessionRepository->save([
            'admin_id' => $admin->id,
            'refresh_token' => hash('sha256', $rawRefreshToken),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'is_trusted' => true,
            'last_activity_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        // 5. Create Audit Log
        $this->adminActivityLogRepository->save([
            'admin_id' => $admin->id,
            'severity' => 'low',
            'action_type' => 'auth',
            'action' => 'Admin logged in',
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        return [
            'success' => true,
            'token' => $token,
            'refresh_token' => $rawRefreshToken,
            'admin' => $admin
        ];
    }

    /**
     * Handles the Two-Factor Authentication flow.
     * Returns an array if a response (error or challenge) is required, otherwise null.
     */
    private function verifyTwoFactor($admin, array $credentials, string $ipAddress, $attempt): ?array
    {
        $google2fa = new Google2FA();

        // Case 1: Initial 2FA setup completion
        if (isset($credentials['setup_secret']) && isset($credentials['two_factor_code'])) {
            $valid = $google2fa->verifyKey($credentials['setup_secret'], $credentials['two_factor_code']);
            if (!$valid) {
                return ['error' => true, 'status' => 401, 'message' => 'Mã xác thực 2FA không chính xác.'];
            }
            $this->adminRepository->update($admin->id, ['two_factor_secret' => $credentials['setup_secret']]);
            return null;
        }

        // Case 2: User hasn't set up 2FA yet - generate and return QR code
        if (!$admin->two_factor_secret) {
            $secretKey = $google2fa->generateSecretKey();
            $qrCodeUrl = $google2fa->getQRCodeUrl('MicroJobAdmin', $admin->email, $secretKey);

            $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
            $writer = new Writer($renderer);
            $svg = $writer->writeString($qrCodeUrl);
            $qrCodeImage = 'data:image/svg+xml;base64,' . base64_encode($svg);

            return [
                'requires_2fa_setup' => true,
                'secret' => $secretKey,
                'qr_code_image' => $qrCodeImage
            ];
        }

        // Case 3: 2FA is set up but code is missing from request
        if (!isset($credentials['two_factor_code'])) {
            return [
                'requires_2fa' => true
            ];
        }

        // Case 4: Verify the provided 2FA code
        $valid = $google2fa->verifyKey($admin->two_factor_secret, $credentials['two_factor_code']);
        if (!$valid) {
            $this->incrementFailedAttempt($admin->email, $ipAddress, $attempt);
            return ['error' => true, 'status' => 401, 'message' => 'Mã xác thực 2FA không chính xác.'];
        }

        return null;
    }

    public function refreshToken(string $rawRefreshToken)
    {
        $session = $this->adminSessionRepository->findByRefreshToken(hash('sha256', $rawRefreshToken));

        if (!$session) {
            return [
                'error' => true,
                'status' => 401,
                'message' => 'Refresh token không hợp lệ hoặc đã hết hạn'
            ];
        }

        $admin = $this->adminRepository->find($session->admin_id);

        if (!$admin || !$admin->is_active) {
            $this->adminSessionRepository->delete($session->id);
            return [
                'error' => true,
                'status' => 401,
                'message' => 'Tài khoản không hợp lệ hoặc đã bị khóa'
            ];
        }

        // Rotate Refresh Token
        $newRawRefreshToken = Str::random(60);
        $this->adminSessionRepository->update($session->id, [
            'refresh_token' => hash('sha256', $newRawRefreshToken),
            'last_activity_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        $token = auth('admin')->login($admin);

        return [
            'success' => true,
            'token' => $token,
            'refresh_token' => $newRawRefreshToken
        ];
    }

    public function logout(?string $rawRefreshToken)
    {
        if ($rawRefreshToken) {
            $this->adminSessionRepository->deleteByRefreshToken(hash('sha256', $rawRefreshToken));
        }

        try {
            auth('admin')->logout();
        } catch (\Exception $e) {
            // Ignore if token is already invalid
        }
    }

    public function logoutAll()
    {
        $admin = auth('admin')->user();

        if ($admin) {
            $this->adminSessionRepository->deleteAllByAdminId($admin->id);

            try {
                auth('admin')->logout();
            } catch (\Exception $e) {
                // Ignore
            }
        }
    }

    public function me()
    {
        return auth('admin')->user();
    }

    public function getSessions()
    {
        $admin = auth('admin')->user();
        return $this->adminSessionRepository->getActiveSessionsByAdminId($admin->id);
    }

    public function revokeSession($id)
    {
        $admin = auth('admin')->user();
        // Here we can either add a check in repository or check here
        // Usually, the repository should handle "where admin_id" for safety
        $this->adminSessionRepository->delete($id);
    }
}

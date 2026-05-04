<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminSession;
use App\Models\FailedLoginAttempt;
use App\Models\AdminActivityLog;
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

    public function login(array $credentials, string $ipAddress, ?string $userAgent)
    {
        $email = $credentials['email'];

        // 1. Check Rate Limiting
        $attempt = FailedLoginAttempt::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->first();

        if ($attempt && $attempt->locked_until && $attempt->locked_until > now()) {
            return [
                'error' => true,
                'status' => 429,
                'message' => 'Tài khoản tạm thời bị khóa do đăng nhập sai quá nhiều lần. Vui lòng thử lại sau.'
            ];
        }

        // 2. Validate Credentials
        $admin = Admin::where('email', $email)->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password_hash)) {
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

        // 3. 2FA Check
        $google2fa = new Google2FA();
        
        if (isset($credentials['setup_secret']) && isset($credentials['two_factor_code'])) {
            $valid = $google2fa->verifyKey($credentials['setup_secret'], $credentials['two_factor_code']);
            if (!$valid) {
                return ['error' => true, 'status' => 401, 'message' => 'Mã xác thực 2FA không chính xác.'];
            }
            $admin->update(['two_factor_secret' => $credentials['setup_secret']]);
        } 
        else if (!$admin->two_factor_secret) {
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
        else if (!isset($credentials['two_factor_code'])) {
            return [
                'requires_2fa' => true
            ];
        } 
        else {
            $valid = $google2fa->verifyKey($admin->two_factor_secret, $credentials['two_factor_code']);
            if (!$valid) {
                $this->incrementFailedAttempt($email, $ipAddress, $attempt);
                return ['error' => true, 'status' => 401, 'message' => 'Mã xác thực 2FA không chính xác.'];
            }
        }

        // 4. Login Success - Clear Attempts
        if ($attempt) {
            $attempt->delete();
        }

        $admin->update(['last_login_at' => now()]);

        // 4. Generate Tokens
        $token = auth('admin')->login($admin);
        
        $rawRefreshToken = Str::random(60);
        
        AdminSession::create([
            'admin_id' => $admin->id,
            'refresh_token' => hash('sha256', $rawRefreshToken),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'is_trusted' => true,
            'last_activity_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        // 5. Audit Log
        AdminActivityLog::create([
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

    private function incrementFailedAttempt($email, $ipAddress, $attempt)
    {
        if (!$attempt) {
            FailedLoginAttempt::create([
                'email' => $email,
                'ip_address' => $ipAddress,
                'attempts' => 1
            ]);
            return;
        }

        $attempts = $attempt->attempts + 1;
        $lockedUntil = $attempts >= self::MAX_ATTEMPTS ? now()->addMinutes(self::LOCKOUT_MINUTES) : null;

        $attempt->update([
            'attempts' => $attempts,
            'locked_until' => $lockedUntil
        ]);
    }

    public function refreshToken(string $rawRefreshToken)
    {
        $session = AdminSession::where('refresh_token', hash('sha256', $rawRefreshToken))
            ->where('expires_at', '>', now())
            ->first();

        if (!$session) {
            return [
                'error' => true,
                'status' => 401,
                'message' => 'Refresh token không hợp lệ hoặc đã hết hạn'
            ];
        }

        $admin = $session->admin;

        if (!$admin || !$admin->is_active) {
            $session->delete();
            return [
                'error' => true,
                'status' => 401,
                'message' => 'Tài khoản không hợp lệ hoặc đã bị khóa'
            ];
        }

        // Rotate Refresh Token
        $newRawRefreshToken = Str::random(60);
        $session->update([
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
            AdminSession::where('refresh_token', hash('sha256', $rawRefreshToken))->delete();
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
            AdminSession::where('admin_id', $admin->id)->delete();
            
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
        return AdminSession::where('admin_id', $admin->id)
            ->select('id', 'ip_address', 'user_agent', 'is_trusted', 'last_activity_at', 'created_at')
            ->get();
    }

    public function revokeSession($id)
    {
        $admin = auth('admin')->user();
        $session = AdminSession::where('admin_id', $admin->id)->findOrFail($id);
        $session->delete();
    }
}

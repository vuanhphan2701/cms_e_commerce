<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user and return tokens.
     */
    public function register(array $data)
    {
        $user = $this->userRepository->save([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = JWTAuth::fromUser($user);
        return $this->respondWithToken($token, $user);
    }

    /**
     * Authenticate user and return tokens.
     */
    public function login(array $credentials)
    {
        if (!$token = auth('api')->attempt($credentials)) {
            return null;
        }

        return $this->respondWithToken($token);
    }

    /**
     * Refresh the current token.
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        auth('api')->logout();
    }

    /**
     * Get the authenticated User.
     */
    public function me()
    {
        return auth('api')->user();
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken($token, $user = null)
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user ?? auth('api')->user()
        ];
    }
}

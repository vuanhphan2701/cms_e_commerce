<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Register a new user and return tokens.
     */
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60
        ];
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
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => auth('api')->user()
        ];
    }
}

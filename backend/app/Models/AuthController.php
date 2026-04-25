<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Core\Controllers\BaseController;
use Core\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return Response::success([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'User registered successfully', 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('name', $request->name)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return Response::error('Thông tin đăng nhập không chính xác.', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return Response::success([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return Response::success(null, 'Đăng xuất thành công');
    }

    public function me(Request $request)
    {
        return Response::success($request->user(), 'Lấy thông tin người dùng thành công');
    }
}

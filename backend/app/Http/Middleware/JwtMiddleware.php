<?php

namespace App\Http\Middleware;

use Closure;
use Core\Response;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return Response::error('Token không hợp lệ', 401);
            } else if ($e instanceof TokenExpiredException) {
                return Response::error('Token đã hết hạn', 401);
            } else {
                return Response::error('Không tìm thấy Token ủy quyền', 401);
            }
        }
        return $next($request);
    }
}

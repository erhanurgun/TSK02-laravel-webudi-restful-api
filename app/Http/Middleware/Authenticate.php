<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return response()->json([
                'error' => [
                    'token' => 'Token bulunamadı veya geçersiz!',
                    'status' => [
                        'message' => 'Bu işlem için gerekli izinlere sahip değilsiniz!',
                        'code' => 401,
                    ],
                ]
            ], 401);
        }
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new HttpResponseException(
            response()->json([
                'error' => [
                    'token' => 'Token bulunamadı veya geçersiz!',
                    'status' => [
                        'message' => 'Bu işlem için gerekli izinlere sahip değilsiniz!',
                        'code' => 401,
                    ],
                ]
            ], 401)
        );
    }
}

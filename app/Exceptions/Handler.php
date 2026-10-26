<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        //
    }

    /**
     * Customize the response for unauthenticated requests.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
		// $user = JWTAuth::parseToken()->authenticate();
        // If the request expects JSON OR is an API route, return JSON
		//dd($request);
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Otherwise, redirect to login page
        return redirect()->guest(route('login'));
    }
}

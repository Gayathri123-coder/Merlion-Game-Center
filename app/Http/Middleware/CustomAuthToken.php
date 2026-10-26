<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomAuthToken
{
    // Replace this with your actual token or load from DB/env
    private $validTokens = [
        '1234567890abcdef',  // example static token
        'abcdef1234567890'
    ];

    public function handle(Request $request, Closure $next)
    {
        // Get token from header
        $token = $request->header('X-CUSTOM-AUTH'); // or Authorization, your choice

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Auth token missing'
            ], 401);
        }

        if (!in_array($token, $this->validTokens)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid auth token'
            ], 401);
        }

        // Token is valid, optionally attach it to request
        $request->merge(['auth_token' => $token]);

        return $next($request);
    }
}

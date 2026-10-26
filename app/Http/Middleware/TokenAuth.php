<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CreateAccountModel;

class TokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken(); // Authorization: Bearer {token}
        $user = CreateAccountModel::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Compatible closure for PHP < 7.4
        $request->setUserResolver(function() use ($user) {
            return $user;
        });

        return $next($request);
    }
}
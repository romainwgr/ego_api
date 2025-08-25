<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->attributes->get('auth_user');


        if (!$user || ($user->role ?? null) !== 'admin') {
            return response()->json(['error' => 'Unauthorized. Admin only.'], 403);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;


class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('jwt_token');

        if (!$token) {
            return response()->json(['error' => 'JWT token missing'], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            // Ajout de l'utilisateur dans la requête
            $user = User::find($decoded->sub);

            if (!$user || !in_array($user->role, ['user', 'admin'])) {
                return response()->json(['error' => 'Unauthorized or inactive user'], 403);
            }

            $request->merge(['auth_user' => $user]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}

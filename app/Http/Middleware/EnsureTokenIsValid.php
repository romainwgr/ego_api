<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1) Récupérer le header Authorization
        $auth = $request->header('Authorization', '');
        if (!str_starts_with($auth, 'Bearer ')) {
            return response()->json(['error' => 'Missing Bearer token'], 401);
        }
        $token = trim(substr($auth, 7));
        if ($token === '') {
            return response()->json(['error' => 'Empty token'], 401);
        }

        // 2) Décoder et vérifier le JWT
        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid token: '.$e->getMessage()], 401);
        }

        // 3) Charger l’utilisateur
        $userId = isset($decoded->sub) ? (int) $decoded->sub : null;
        if (!$userId) {
            return response()->json(['error' => 'Invalid token subject'], 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 401);
        }

        // 4) Attacher l’utilisateur à la requête et au système d’auth
        // - accessible via request()->get('auth_user')
        $request->attributes->set('auth_user', $user);

        // - et via Auth::user()
        Auth::setUser($user);

        return $next($request);
    }
}

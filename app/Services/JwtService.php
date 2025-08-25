<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;

class JwtService
{
    /**
     * Crée un JWT d'accès court pour un utilisateur.
     * TTL configurable via env JWT_TTL (minutes). Par défaut 15 min.
     */
    public function createTokenForUser(User $user): string
    {
        $ttlMinutes = (int) env('JWT_TTL', 15);
        $now        = time();
        $exp        = $now + ($ttlMinutes * 60);
        $issuer     = config('app.url');  // ex: https://api.example.com
        $audience   = parse_url(config('app.url'), PHP_URL_HOST) ?: 'api';

        $payload = [
            'iss'   => $issuer,
            'aud'   => $audience,
            'sub'   => (string) $user->id,
            'iat'   => $now,
            'nbf'   => $now,
            'exp'   => $exp,
            'jti'   => bin2hex(random_bytes(12)),
            // Claims utiles côté front (sans info sensible)
            'email' => $user->email,
            'uid'   => (int) $user->id,
        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }
}

<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;

class JwtService
{
    public function createTokenForUser(User $user): string
    {
        $payload = [
            'iss'   => url('/'),
            'sub'   => $user->id,
            'iat'   => time(),
            'exp'   => time() + 3600,
            'email' => $user->email,
            'status' => $user->status,

        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }
}

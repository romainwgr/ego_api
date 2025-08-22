<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;


public function refresh(Request $request)
{
    $refreshTokenPlain = $request->cookie('refresh_token');

    if (!$refreshTokenPlain) {
        return response()->json(['error' => 'Refresh token missing'], 401);
    }

    // Rechercher le token hashé en base qui correspond
    $tokenRecord = RefreshToken::where('revoked', false)
        ->where('expires_at', '>', Carbon::now())
        ->get()
        ->first(function ($token) use ($refreshTokenPlain) {
            return Hash::check($refreshTokenPlain, $token->token);
        });

    if (!$tokenRecord) {
        return response()->json(['error' => 'Invalid or expired refresh token'], 401);
    }

    $user = $tokenRecord->user;

    // Générer un nouveau JWT
    $jwt = $this->jwtService->createTokenForUser($user);

    $cookieJwt = cookie(
        'jwt_token',
        $jwt,
        60,
        '/',
        'localhost',
        false,
        true,
        false,
        'Lax'
    );

    return response()->json([
        'success' => true,
        'message' => 'Token refreshed'
    ])->cookie($cookieJwt);
}
public function logout(Request $request)
{
    $refreshTokenPlain = $request->cookie('refresh_token');

    if ($refreshTokenPlain) {
        $tokenRecord = RefreshToken::where('revoked', false)
            ->get()
            ->first(function ($token) use ($refreshTokenPlain) {
                return Hash::check($refreshTokenPlain, $token->token);
            });

        if ($tokenRecord) {
            $tokenRecord->revoked = true;
            $tokenRecord->save();
        }
    }

    // Supprimer cookies côté client
    return response()->json(['success' => true])
        ->cookie(cookie()->forget('jwt_token'))
        ->cookie(cookie()->forget('refresh_token'));
}


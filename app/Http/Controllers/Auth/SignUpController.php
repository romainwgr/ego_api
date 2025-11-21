<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RefreshToken;    
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\JwtService;
use Illuminate\Support\Str;      
use Carbon\Carbon;               

class SignUpController extends Controller
{
    /**
     * POST /signup
     * Crée l’utilisateur (email + mot de passe), puis le connecte.
     */
    public function signup(Request $request, JwtService $jwtService)
    {
        // 1. Validation
        $data = $request->validate([
            'email'    => ['required', 'string', 'email:rfc,dns', Rule::unique(User::class)],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // 2. Création de l’utilisateur
        $user = User::create([
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'password_algo' => config('hashing.driver'),
        ]);

        // 3. Création du JWT
        $jwt = $jwtService->createTokenForUser($user);

        // 3bis. Création du refresh token
        $secret  = Str::random(64);
        $tokenId = (string) Str::uuid();

        RefreshToken::create([
            'user_id'    => $user->id,
            'token_id'   => $tokenId,
            'token_hash' => Hash::make($secret),
            'expires_at' => Carbon::now()->addDays((int) env('REFRESH_TTL_DAYS', 7)),
            'user_agent' => request()->userAgent(),
            'ip'         => request()->ip(),
        ]);

        // 4. Réponse
        return response()
            ->json([
                'success' => true,
                'jwt'     => $jwt,
                'user'    => [
                    'id'    => $user->id,
                    'email' => $user->email,
                ],
            ])
            ->header('X-Refresh-Token', $tokenId . '.' . $secret);
    }
}

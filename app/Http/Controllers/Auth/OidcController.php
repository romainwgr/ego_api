<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use App\Services\JwtService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\RefreshToken; 
use Carbon\Carbon;


class OidcController extends Controller
{
    private $jwtSecret;
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService) 
    {
        $this->jwtSecret = env('JWT_SECRET');
        $this->jwtService = $jwtService; 
    }
    public function redirectToGoogle()
    {

        try {
            return Socialite::driver('google')->stateless()->redirect(); // ⬅️ Ajout de stateless()
        } catch (\Exception $e) {
            Log::error('Erreur Socialite Google (redirect): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'google_redirect_failed',
                'message' => 'Erreur lors de la redirection vers Google.'
            ], 500);
        }
    }

    public function callbackToGoogle(Request $request)
{

    try {
        Log::info('Tentative récupération utilisateur Google...');
        $googleUser = Socialite::driver('google')->stateless()->user();
        Log::info('Utilisateur Google récupéré', ['user' => $googleUser]);

        $firstName = $this->extractFirstName($googleUser->getName());
        $lastName  = $this->extractLastName($googleUser->getName());

        $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'email' => $googleUser->getEmail(),
                    'first_name'  => $firstName,
                    'last_name' => $lastName
                ]
            );


        $jwt = $this->jwtService->createTokenForUser($user);


        $refreshTokenPlain = Str::random(64);

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => Hash::make($refreshTokenPlain),
            'expires_at' => Carbon::now()->addDays(7),
            'revoked' => false,
        ]);

        $isSecure = env('APP_ENV') === 'production';

        // 5. Renvoyer le refresh token en cookie HttpOnly ou dans la réponse
        $cookieRefresh = cookie(
            'refresh_token',
            $refreshTokenPlain,
            60 * 24 * 7, // 7 jours
            '/',
            env('COOKIE_DOMAIN'),
            $isSecure,
            true,
            false,
            'Lax'
        );

        $cookieJwt  = cookie(
            'jwt_token',
            $jwt,
            60,                // durée en minutes
            '/',               // path
            env('COOKIE_DOMAIN'), // domaine (cross-subdomain)
            $isSecure,              // secure (HTTPS)
            true,              // httpOnly
            false,             // raw
            'Lax'              // SameSite
        );

            // Renvoie une réponse JSON avec cookie
            return response()->json([
                'success' => true,
                'user' => [
                    'id'       => $user->id,
                    'email'    => $user->email,
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,

                ],
            ])->cookie($cookieJwt)->cookie($cookieRefresh);


        // Ici, ton code de login / création d’utilisateur...
    } catch (\Exception $e) {
        Log::error('Erreur Socialite Google (callback): ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'error' => 'google_authentication_failed',
            'message' => 'Erreur lors de la connexion avec Google.',
        ], 500);
    }
}
    private function extractFirstName(string $fullName): string
{
    return explode(' ', $fullName)[0] ?? '';
}

private function extractLastName(string $fullName): string
{
    $parts = explode(' ', $fullName);
    array_shift($parts); // retire le prénom
    return implode(' ', $parts); // le reste = nom
}


}

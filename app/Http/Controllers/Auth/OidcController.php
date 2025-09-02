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
            return Socialite::driver('google')->stateless()->redirect(); //  Ajout de stateless()
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
        $googleUser = Socialite::driver('google')->stateless()->user();

        $firstName = $this->extractFirstName($googleUser->getName());
        $lastName  = $this->extractLastName($googleUser->getName());

        $user = User::updateOrCreate(
            ['google_id' => $googleUser->getId()],
            [
                'email'      => $googleUser->getEmail(),
                'first_name' => $firstName,
                'last_name'  => $lastName,
            ]
        );

        // Génère un one-time code (OTC) valable 60s
        $code = Str::random(48);
        Cache::put("otc:$code", ['user_id' => $user->id], now()->addMinute());

        $front = rtrim(env('APP_FRONTEND_URL'), '/');
        return redirect()->to($front . '/auth/#success?code=' . $code);

    } catch (\Throwable $e) {
        Log::error('Error Socialite Google (callback): '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        // Option : rediriger vers ta page login avec un message
        $front = rtrim(env('APP_FRONTEND_URL'), '/');
        return redirect()->to($front . '/auth/#login?error=google');
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
public function exchange(Request $request)
{
    $code = (string) $request->input('code');

    // Récupère et détruit le code one-time
    $data = Cache::pull("otc:$code");
    if (!$data || !isset($data['user_id'])) {
        return response()->json([
            'success' => false,
            'error'   => 'invalid_code',
            'message' => 'Invalid or expired one-time code',
        ], 401);
    }

    $user = User::findOrFail($data['user_id']);

    // 1) Génère un JWT
    $jwt = $this->jwtService->createTokenForUser($user);

    // 2) Génère le refresh token split
    $secret  = Str::random(64);      // jamais stocké en clair
    $tokenId = (string) Str::uuid(); // identifiant public

    RefreshToken::create([
        'user_id'    => $user->id,
        'token_id'   => $tokenId,
        'token_hash' => Hash::make($secret),
        'expires_at' => Carbon::now()->addDays((int) env('REFRESH_TTL_DAYS', 7)),
        'user_agent' => $request->userAgent(),
        'ip'         => $request->ip(),
        'revoked'    => false,
    ]);

    return response()
        ->json([
            'success' => true,
            'jwt'     => $jwt,
            'user'    => [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ],
        ])
        ->header('Access-Control-Expose-Headers', 'X-Refresh-Token')
        ->header('X-Refresh-Token', $tokenId . '.' . $secret);
}


}

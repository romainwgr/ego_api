<?php 

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;   
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Services\JwtService;
use App\Models\RefreshToken;
use Illuminate\Support\Carbon;



class ResetPasswordController extends Controller
{
    /** POST /forgot-password */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        Password::sendResetLink($request->only('email'));

        // Pour des raisons de sécurité, on ne révèle jamais si l'email existe ou non dans la base de données.
        // On retourne donc toujours une réponse 200 avec un message générique.
        return response()->json([
            'message' => "Si un compte est associé à cet email, un lien de réinitialisation a été envoyé."
        ], 200);
    }
    public function redirectToReset($token)
    {
        $email = request()->query('email');

        $url = config('app.frontend_url')
            . '/reset-password?token=' . $token
            . '&email=' . urlencode($email);

        return redirect()->away($url);
    }


/** POST /reset-password */
public function reset(Request $request, JwtService $jwtService)
{
    $request->validate([
        'token'    => ['required'],
        'email'    => ['required','email'],
        'password' => ['required','string','min:8','confirmed'],
    ]);

    $jwt = null;
    $newRefreshHeader = null; // contiendra "<tokenId>.<secret>"
    $userUpdated = null;

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) use (&$jwt, &$userUpdated, &$newRefreshHeader, $jwtService) {

            // 1) Réinitialisation du mot de passe
            $user->forceFill([
                'password'       => Hash::make($password),
                'password_algo'  => config('hashing.driver'), // garde une trace de l’algo
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
            $userUpdated = $user;

            // 2) Sécurité: révoquer tous les anciens refresh tokens
            RefreshToken::where('user_id', $user->id)->update(['revoked_at' => now()]);

            // 3) Émettre un nouveau JWT d'accès
            $jwt = $jwtService->createTokenForUser($user);

            // 4) Émettre un nouveau refresh token (stocké en DB, hashé) + renvoyé en header
            $secret  = Str::random(64);           // jamais stocké en clair côté serveur
            $tokenId = (string) Str::uuid();

            RefreshToken::create([
                'user_id'    => $user->id,
                'token_id'   => $tokenId,
                'token_hash' => Hash::make($secret),
                'expires_at' => Carbon::now()->addDays((int) env('REFRESH_TTL_DAYS', 7)),
            ]);

            $newRefreshHeader = $tokenId . '.' . $secret; // à renvoyer en header
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return response()
            ->json([
                'message' => __('passwords.' . $status),
                'jwt'     => $jwt,
                'user'    => [
                    'id'    => $userUpdated->id,
                    'email' => $userUpdated->email,
                ],
            ], 200)
            ->header('X-Refresh-Token', $newRefreshHeader);
    }

    return response()->json([
        'error'   => __('passwords.' . $status),
        'message' => 'Échec de la réinitialisation du mot de passe.',
    ], 400);
}

}
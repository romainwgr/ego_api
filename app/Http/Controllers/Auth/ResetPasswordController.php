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


class ResetPasswordController extends Controller
{
    /** POST /forgot-password */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        // Pour un front AJAX : réponse JSON neutre (toujours 200)
        return response()->json(
            ['message' => __($status)],
            $status === Password::RESET_LINK_SENT ? 200 : 422
        );
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
        'token'    => 'required',
        'email'    => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $jwt = null;
    $refreshTokenPlain = null;
    $userUpdated = null;

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) use (&$jwt, &$userUpdated, $jwtService, &$refreshTokenPlain) {
            // Réinitialisation
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));

            $userUpdated = $user;

            // Génération JWT
            $jwt = $jwtService->createTokenForUser($user);

            // Génération du refresh token
            $refreshTokenPlain = Str::random(64);

            RefreshToken::create([
                'user_id' => $user->id,
                'token' => Hash::make($refreshTokenPlain),
                'expires_at' => Carbon::now()->addDays(7),
                'revoked' => false,
            ]);
        }
    );

    $isSecure = env('APP_ENV') === 'production';

    if ($status === Password::PASSWORD_RESET) {
        $cookieJwt = cookie(
            'jwt_token',
            $jwt,
            60, // 1h
            '/',
            env('COOKIE_DOMAIN'),
            $isSecure,
            true,
            false,
            'Lax'
        );

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

        return response()->json([
            'message' => __('passwords.' . $status),
            'user' => [
                'id'    => $userUpdated->id,
                'email' => $userUpdated->email,
            ]
        ], 200)->cookie($cookieJwt)->cookie($cookieRefresh);
    }

    return response()->json([
        'error'   => __('passwords.' . $status),
        'message' => 'Échec de la réinitialisation du mot de passe.',
    ], 400);
}
}
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private JwtService $jwt) {}

    /**
     * POST /auth/refresh
     * Attend un header 'X-Refresh-Token: <tokenId>.<secret>' (ou body refresh_token)
     * Valide, ROTATE, renvoie un nouveau JWT (body) + nouveau refresh (header).
     */
    public function refresh(Request $request)
    {
        $presented = $request->header('X-Refresh-Token') ?? $request->input('refresh_token');
        if (!$presented || !str_contains($presented, '.')) {
            return response()->json(['success'=>false,'error'=>'missing_refresh'], 401);
        }

        [$tokenId, $secret] = explode('.', $presented, 2);

        /** @var RefreshToken|null $rt */
        $rt = RefreshToken::where('token_id', $tokenId)->first();
        if (!$rt || $rt->revoked_at || now()->gte($rt->expires_at)) {
            return response()->json(['success'=>false,'error'=>'invalid_or_expired'], 401);
        }

        if (!Hash::check($secret, $rt->token_hash)) {
            // Reuse / altération détectée: révoquer ce token
            $rt->update(['revoked_at' => now()]);
            return response()->json(['success'=>false,'error'=>'invalid_secret'], 401);
        }

        $user = $rt->user;
        if (!$user) {
            $rt->update(['revoked_at' => now()]);
            return response()->json(['success'=>false,'error'=>'user_not_found'], 404);
        }

        // Nouveau JWT
        $newJwt = $this->jwt->createTokenForUser($user);

        // Rotation refresh
        $rt->update(['revoked_at' => now()]);

        $newSecret = Str::random(64);
        $newId     = (string) Str::uuid();

        RefreshToken::create([
            'user_id'    => $user->id,
            'token_id'   => $newId,
            'token_hash' => Hash::make($newSecret),
            'expires_at' => Carbon::now()->addDays((int) env('REFRESH_TTL_DAYS', 7)),
            'user_agent' => $request->userAgent(),
            'ip'         => $request->ip(),
        ]);

        return response()
            ->json(['success'=>true, 'jwt'=>$newJwt])
            ->header('X-Refresh-Token', $newId.'.'.$newSecret);
    }

    /**
     * POST /auth/logout
     * Révoque le refresh présenté (ou tous si 'all' = true). Le front efface ses tokens.
     */
    public function logout(Request $request)
    {
        $all = filter_var($request->boolean('all'), FILTER_VALIDATE_BOOLEAN);

        if ($all && Auth::check()) {
            RefreshToken::where('user_id', Auth::id())->update(['revoked_at'=>now()]);
        } else {
            $presented = $request->header('X-Refresh-Token') ?? $request->input('refresh_token');
            if ($presented && str_contains($presented, '.')) {
                [$tokenId] = explode('.', $presented, 2);
                RefreshToken::where('token_id', $tokenId)->update(['revoked_at'=>now()]);
            }
        }

        try { Auth::logout(); } catch (\Throwable $e) {}

        return response()->json(['success'=>true]);
    }

    /**
     * GET /auth/me
     * Nécessite le JWT d'accès (Authorization: Bearer ...).
     */
    public function me(Request $request)
    {
        return response()->json(Auth::user());
    }
}

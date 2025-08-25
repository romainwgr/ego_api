<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JwtService;
use Illuminate\Support\Str;
use App\Models\RefreshToken; 
use Carbon\Carbon;

class LoginController extends Controller
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }


    public function login(Request $request)
    {
        $data = $this->validateLogin($request);
        $users = $this->retrieveUsers($data['login']);

        $valid = $users->filter(fn($u) => $this->isPasswordValid($u, $data['password']));

        if ($valid->isEmpty()) {
            return $this->onFailure();
        }

        if ($valid->count() > 1) {
            return $this->onMultiple($valid, $data);
        }

        return $this->authenticate($valid->first(), $data['password']);
    }

    protected function validateLogin(Request $r): array
    {
        return $r->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
    }

    protected function retrieveUsers(string $login)
    {
        $field = $this->detectField($login);
        return User::where($field, $login)->get();
    }

    protected function detectField(string $login): string
    {
        return filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    }

    protected function isPasswordValid(User $user, string $plain): bool
    {
        if ($user->password_algo === 'md5') {
            return md5($plain) === $user->password;
        }

        return Hash::check($plain, $user->password);
    }

    protected function onFailure()
    {
        return response()->json([
            'success' => false,
            'error' => 'invalid_credentials',
            'message' => 'Identifiant ou mot de passe incorrect.'
        ], 401);
    }

    protected function onMultiple($validUsers, array $data)
    {
        return response()->json([
            'success' => false,
            'error' => 'multiple_accounts',
            'message' => 'Plusieurs comptes sont associés à cet identifiant.',
            'accounts' => $validUsers->map(fn($u) => [
                'id' => $u->id,
                'username' => $u->username,
                'email' => $u->email,
            ])
        ], 409);
    }


    protected function authenticate(User $user, string $plain)
{
    $this->lazyRehash($user, $plain);

    $jwt = $this->jwtService->createTokenForUser($user);

    $secret  = Str::random(64);       // jamais stocké en clair côté serveur
    $tokenId = (string) Str::uuid();  // identifiant public

    RefreshToken::create([
        'user_id'    => $user->id,
        'token_id'   => $tokenId,
        'token_hash' => Hash::make($secret),
        'expires_at' => Carbon::now()->addDays((int) env('REFRESH_TTL_DAYS', 7)),
        'user_agent' => request()->userAgent(),
        'ip'         => request()->ip(),
    ]);

    // Réponse : JWT dans le body, refresh en HEADER
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
        ->header('X-Refresh-Token', $tokenId . '.' . $secret);
    
    }


    protected function lazyRehash(User $user, string $plain): void
    {
        $algo = config('hashing.driver');

        if ($user->password_algo !== $algo) {
            $user->password = Hash::driver($algo)->make($plain);
            $user->password_algo = $algo;
            $user->save();
        }
    }

    public function chooseAccount(Request $request)
    {
        $user = $this->findChosenUser($request);
        return $this->authenticate($user, $request->input('password'));
    }
    
    protected function findChosenUser(Request $r): User
    {
        $r->validate([
            'user_id'  => 'required|integer|exists:users,id',
            'password' => 'required|string',
        ]);
    
        return User::findOrFail($r->input('user_id'));
    }
   

}

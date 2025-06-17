<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\JwtService;

class SignUpController extends Controller
{
    /**
     * POST /signup
     * Crée l’utilisateur (email + mot de passe), puis le connecte.
     */
    public function signup(Request $request,JwtService $jwtService)
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
        'password_algo' => config('hashing.driver'), // utile si tu veux la logique lazyRehash
    ]);

    // // 3. Génération du JWT


    //Fix la génération du JWT est lors de la connexion, pas à l'inscription



    // $jwt = $jwtService->createTokenForUser($user);

    // $cookie = cookie(
    //     'jwt_token',
    //     $jwt,
    //     60,
    //     '/',
    //     '.ego-network.com',
    //     true,
    //     true,
    //     false,
    //     'Lax'
    // );

    // 4. Réponse
    return response()->json([
        'success' => true,
        'user' => [
            'id'    => $user->id,
            'email' => $user->email,
        ]
        ]);
    // ->cookie($cookie);
}


}

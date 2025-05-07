<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    // Envoir le formulaire de connexion
    public function showLoginForm()
    {
        return view('auth.login');
    }


    // Traiter le formulaire de connexion
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
        return back()
            ->withErrors(['login' => 'Identifiant ou mot de passe incorrect'])
            ->withInput();
    }

    protected function onMultiple($validUsers, array $data)
    {
        return view('auth.choose-account', [
            'users'    => $validUsers,
            'login'    => $data['login'],
            'password' => $data['password'],
        ]);
    }

    protected function authenticate(User $user, string $plain)
    {
        $this->lazyRehash($user, $plain);
        Auth::login($user);
        return view('home');
    }

    protected function lazyRehash(User $user, string $plain): void
    {
        $algo = config('hashing.driver');
        if ($user->password_algo !== $algo) {
            $user->password      = Hash::driver($algo)->make($plain);
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

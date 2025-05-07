<?php
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Auth\OidcController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Affiche le formulaire (GET)
Route::get('login', [LoginController::class, 'showLoginForm'])
     ->middleware('guest')
     ->name('login');

// 2. Traiter le formulaire de connexion (POST /login)
Route::post('login', [LoginController::class, 'login'])
    ->middleware(['guest', 'throttle:5,1']);

Route::post('login/choose',[LoginController::class,'chooseAccount'])->middleware('guest');

// Affiche le formulaire "Mot de passe oublié"
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
     ->name('password.request');

// Envoie l’e-mail avec le lien
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
     ->name('password.email');

// Affiche le formulaire de saisie du nouveau mot de passe
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
     ->name('password.reset');

// Traite la soumission du nouveau mot de passe
Route::post('password/reset', [ResetPasswordController::class, 'reset'])
     ->name('password.update');









/* ─────── Connexion / création avec Google ─────── */

Route::get('/auth/google', [OidcController::class, 'redirectToGoogle'])
     ->name('google.login');
Route::get('/auth/google/callback',[OidcController::class,'callbackToGoogle']);


Route::post('logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');







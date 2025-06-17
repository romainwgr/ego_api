<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController; 
use App\Http\Controllers\Auth\SignUpController; 
use App\Http\Controllers\Auth\ResetPasswordController; 


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ici vous pouvez enregistrer les routes de votre API. Ces routes sont
| automatiquement chargées par le RouteServiceProvider dans un groupe
| qui est attribué au middleware "api".
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/choose-account', [LoginController::class, 'chooseAccount']);

    Route::post('/signup', [SignUpController::class, 'signup']);

    Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetLink']);
    Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);


});

// Routes utilisateur connecté (peu importe le rôle)
Route::middleware('auth.jwt')->group(function () {
    Route::get('/profile', function () {
        return response()->json([
            'user' => request()->get('auth_user')
        ]);
    });
});

// Routes réservées à l'admin
Route::middleware('auth.admin')->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json([
            'message' => 'Bienvenue Admin',
            'admin' => request()->get('auth_user')
        ]);
    });
});





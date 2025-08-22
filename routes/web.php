<?php

use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Auth\OidcController;
use App\Http\Controllers\Auth\ResetPasswordController; 




Route::prefix('auth')->group(function () {
    Route::get('/google/redirect', [OidcController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [OidcController::class, 'callbackToGoogle']);
});

// Redirection vers le frontend après clic sur lien e-mail de reset
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'redirectToReset'])
    ->name('password.reset');

Route::get('/', function () {
    return response()->json([
        'message' => "Welcome to the EGO API"
    ]);
});

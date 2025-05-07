<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Route protégée avec Sanctum
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Ta route de test pour récupérer tous les users
Route::get('/users', [UserController::class, 'index']);
// Route pour récupérer un utilisateur spécifique
Route::get('/users/{id}', [UserController::class, 'show']);
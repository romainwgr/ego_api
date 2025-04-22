

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Get all users
Route::get('/users', [UserController::class, 'index']);

// Get a single user by ID
Route::get('/users/{id}', [UserController::class, 'show']);

// Create a new user
Route::post('/users', [UserController::class, 'store']);

// Update an existing user
Route::put('/users/{id}', [UserController::class, 'update']);

// Delete a user
Route::delete('/users/{id}', [UserController::class, 'destroy']);
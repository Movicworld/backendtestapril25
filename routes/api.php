<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Expense Management API'
    ], 200);
});

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Expenses – all authenticated users
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);

    // Expenses – Managers + Admins
    Route::middleware('role:Manager,Admin')->group(function () {
        Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    });

    // Expenses – Admins only
    Route::middleware('role:Admin')->group(function () {
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
    });
});

<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

// ── Public API route ─────────────────────────────────────────────────────────
// throttle:5,1 = max 5 requests per 1 minute per IP — prevents brute force
// Returns a Sanctum token the client stores and sends as: Bearer <token>
Route::post('/login', [ApiController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('api.login');

// ── Protected API routes — require a valid Sanctum token ─────────────────────
// Send token in header: Authorization: Bearer <your-token>
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::delete('/logout', [ApiController::class, 'logout'])->name('api.logout');

    // Groups
    Route::get('/groups',        [ApiController::class, 'groups'])->name('api.groups.index');
    Route::post('/groups',       [ApiController::class, 'storeGroup'])->name('api.groups.store');
    Route::get('/groups/{group}',[ApiController::class, 'showGroup'])->name('api.groups.show');

    // Expenses — uses StoreExpenseRequest (handles authorize + validation)
    Route::post('/expenses',     [ApiController::class, 'storeExpense'])->name('api.expenses.store');

    // Friends
    Route::get('/friends',       [ApiController::class, 'friends'])->name('api.friends');
});

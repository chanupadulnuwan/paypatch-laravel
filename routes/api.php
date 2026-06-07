<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('api.login');

Route::post('/register', [ApiController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('api.register');

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [ApiController::class, 'logout'])->name('api.logout');
    Route::get('/exchange-rates/usd-lkr', [ApiController::class, 'usdLkrRate'])->name('api.exchange.usdLkr');
    Route::get('/users/search', [ApiController::class, 'searchUsers'])->name('api.users.search');

    Route::get('/groups', [ApiController::class, 'groups'])->name('api.groups.index');
    Route::post('/groups', [ApiController::class, 'storeGroup'])->name('api.groups.store');
    Route::get('/groups/{group}', [ApiController::class, 'showGroup'])->name('api.groups.show');
    Route::post('/groups/{group}/update', [ApiController::class, 'updateGroup'])->name('api.groups.update');
    Route::delete('/groups/{group}', [ApiController::class, 'destroyGroup'])->name('api.groups.destroy');
    Route::post('/groups/{group}/members', [ApiController::class, 'addGroupMember'])->name('api.groups.members.store');
    Route::delete('/groups/{group}/members/{user}', [ApiController::class, 'removeGroupMember'])->name('api.groups.members.destroy');

    Route::post('/expenses', [ApiController::class, 'storeExpense'])->name('api.expenses.store');
    Route::delete('/expenses/{expense}', [ApiController::class, 'destroyExpense'])->name('api.expenses.destroy');

    Route::post('/groups/{group}/settle', [ApiController::class, 'storeSettlement'])->name('api.groups.settle');
    Route::post('/groups/{group}/remind', [ApiController::class, 'sendReminder'])->name('api.groups.remind');
    Route::post('/groups/{group}/leave', [ApiController::class, 'leaveGroup'])->name('api.groups.leave');

    Route::get('/friends', [ApiController::class, 'friends'])->name('api.friends');
});

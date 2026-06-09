<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('api.login');

Route::post('/register', [ApiController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('api.register');

Route::post('/otp/send', [ApiController::class, 'sendOtp'])
    ->middleware('throttle:5,1')
    ->name('api.otp.send');

Route::post('/otp/verify', [ApiController::class, 'verifyOtp'])
    ->middleware('throttle:10,1')
    ->name('api.otp.verify');

Route::post('/auth/google', [ApiController::class, 'googleAuth'])
    ->middleware('throttle:5,1')
    ->name('api.auth.google');

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [ApiController::class, 'logout'])->name('api.logout');
    Route::get('/exchange-rates/usd-lkr', [ApiController::class, 'usdLkrRate'])->name('api.exchange.usdLkr');
    Route::get('/users/search', [ApiController::class, 'searchUsers'])->name('api.users.search');
    Route::post('/users/match-phones', [ApiController::class, 'matchByPhone'])->name('api.users.matchPhones');
    Route::post('/profile/update', [ApiController::class, 'updateProfile'])->name('api.profile.update');
    Route::get('/profile', [ApiController::class, 'getProfile'])->name('api.profile.get');
    Route::get('/activity', [ApiController::class, 'getActivity'])->name('api.activity');
    Route::get('/activity/unread-count', [ApiController::class, 'getUnreadCount'])->name('api.activity.unreadCount');
    Route::post('/change-password', [ApiController::class, 'changePassword'])->name('api.changePassword');

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
    Route::delete('/groups/{group}/leave', [ApiController::class, 'leaveGroup'])->name('api.groups.leave.delete');

    Route::get('/friends', [ApiController::class, 'friends'])->name('api.friends');
    Route::post('/friends/invite', [ApiController::class, 'inviteFriend'])->name('api.friends.invite');
    Route::post('/friends/{id}/accept', [ApiController::class, 'acceptFriendRequest'])->name('api.friends.accept');
    Route::post('/friends/{id}/decline', [ApiController::class, 'declineFriendRequest'])->name('api.friends.decline');

    // Posts (group owner stories)
    Route::get('/posts', [PostController::class, 'index'])->name('api.posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('api.posts.store');
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('api.posts.like');
    Route::get('/posts/{post}/comments', [PostController::class, 'comments'])->name('api.posts.comments');
    Route::post('/posts/{post}/comments', [PostController::class, 'addComment'])->name('api.posts.comment');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('api.posts.destroy');
});

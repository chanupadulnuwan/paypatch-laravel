<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\EmailTwoFactorController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

// Public landing page
Route::get('/', fn () => view('welcome'))->name('home');

Route::get('/debug-db', function() {
    return [
        'env_db_connection' => env('DB_CONNECTION'),
        'env_database_url' => env('DATABASE_URL'),
        'env_db_url' => env('DB_URL'),
        'env_db_database' => env('DB_DATABASE'),
        'env_google_client_id' => env('GOOGLE_CLIENT_ID') ? substr(env('GOOGLE_CLIENT_ID'), 0, 15) . '...' : null,
        'config_google_client_id' => config('services.google.client_id') ? substr(config('services.google.client_id'), 0, 15) . '...' : null,
        'config_pgsql' => config('database.connections.pgsql'),
    ];
});

// 2FA Verification Routes
Route::get('/login/two-factor', [EmailTwoFactorController::class, 'show'])->name('login.two-factor');
Route::post('/login/two-factor', [EmailTwoFactorController::class, 'verify'])->name('login.two-factor.verify');

// Google OAuth Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ── Protected routes — must be logged in ──────────────────
Route::middleware(['auth:sanctum', config('jetstream.auth_session')])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Groups — resource controller gives us index, create, store, show, update, destroy
        Route::resource('groups', GroupController::class);
        // Extra group actions (not part of standard resource)
        Route::post('/groups/{group}/addMember',      [GroupController::class, 'addMember'])->name('groups.addMember');
        Route::post('/groups/{group}/removeMember',   [GroupController::class, 'removeMember'])->name('groups.removeMember');
        Route::post('/groups/{group}/settle',         [GroupController::class, 'settle'])->name('groups.settle');
        Route::post('/groups/{group}/settle-request', [GroupController::class, 'requestSettle'])->name('groups.settleRequest');

        // Expenses — only need create, store, destroy, update from the resource
        Route::resource('expenses', ExpenseController::class)->only(['create', 'store', 'destroy', 'update']);

        // Friends page
        Route::get('/friends', [FriendsController::class, 'index'])->name('friends');

        // Activity feed
        Route::get('/activity', [ActivityController::class, 'index'])->name('activity');

        // Profile update
        Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

        // Admin routes — extra EnsureAdmin middleware blocks non-admins
        Route::middleware('admin')->prefix('admin')->name('admin')->group(function () {
            Route::get('/',                          [AdminController::class, 'index'])->name('');  // /admin
            Route::get('/packages',                  [AdminController::class, 'packages'])->name('.packages');
            Route::post('/packages',                 [AdminController::class, 'storePackage'])->name('.storePackage');
            Route::put('/packages/{package}',        [AdminController::class, 'updatePackage'])->name('.updatePackage');
            Route::delete('/packages/{package}',     [AdminController::class, 'deletePackage'])->name('.deletePackage');
            Route::get('/activity',                  [AdminController::class, 'activity'])->name('.activity');
            Route::get('/insights',                  [AdminController::class, 'insights'])->name('.insights');
            Route::post('/settings',                 [AdminController::class, 'updateSettings'])->name('.settings');
            Route::delete('/users/{user}',           [AdminController::class, 'deleteUser'])->name('.deleteUser');
            Route::post('/users/{user}/ban',         [AdminController::class, 'banUser'])->name('.banUser');
            Route::post('/users/{user}/toggle-type', [AdminController::class, 'toggleAccountType'])->name('.toggleAccountType');
        });
    });

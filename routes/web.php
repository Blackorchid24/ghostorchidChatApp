<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;


Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('chat.index')
        : redirect()->route('login');
})->name('home');

Route::get('/auth/google', [
    GoogleAuthController::class,
    'redirect',
])->name('google.redirect');

Route::get('/auth/google/callback', [
    GoogleAuthController::class,
    'callback',
])->name('google.callback');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [
        LoginController::class,
        'store',
    ])->name('login.store');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', [
        RegisterController::class,
        'store',
    ])->name('register.store');
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [
        LoginController::class,
        'destroy',
    ])->name('logout');

    Route::get('/chat', [
        ChatController::class,
        'index',
    ])->name('chat.index');

    Route::post('/chat/private', [
        ChatController::class,
        'startPrivate',
    ])->name('chat.private');

    Route::post('/chat/group', [
        ChatController::class,
        'createGroup',
    ])->name('chat.group');

    Route::post('/chat/{conversation}/messages', [
        ChatController::class,
        'sendMessage',
    ])->name('chat.messages.store');

    Route::post('/chat/{conversation}/members', [
        ChatController::class,
        'addMember',
    ])->name('chat.members.add');

    Route::delete('/chat/{conversation}/members/{user}', [
        ChatController::class,
        'removeMember',
    ])->name('chat.members.remove');

    Route::patch('/chat/{conversation}', [
        ChatController::class,
        'updateConversation',
    ])->name('chat.update');

    Route::delete('/chat/{conversation}', [
        ChatController::class,
        'destroyConversation',
    ])->name('chat.destroy');

    Route::get('/chat/{conversation}', [
        ChatController::class,
        'show',
    ])->name('chat.show');

    Route::get('/profile', [
        ProfileController::class,
        'edit',
    ])->name('profile.edit');

    Route::put('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');
});


Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/login', [
            AdminAuthController::class,
            'create',
        ])->name('login');

        Route::post('/login', [
            AdminAuthController::class,
            'store',
        ])->name('login.store');

        Route::middleware('admin.auth')->group(function () {

            Route::post('/logout', [
                AdminAuthController::class,
                'destroy',
            ])->name('logout');

            Route::get('/dashboard', [
                AdminDashboardController::class,
                'index',
            ])->name('dashboard');

            Route::get('/users', [
                AdminUserController::class,
                'index',
            ])->name('users.index');

            Route::patch('/users/{user}/status', [
                AdminUserController::class,
                'toggleStatus',
            ])->name('users.status');

            Route::delete('/users/{user}', [
                AdminUserController::class,
                'destroy'
            ])->name('users.destroy');
        });
    });

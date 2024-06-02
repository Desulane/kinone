<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::get('/create-session', [SessionController::class, 'showCreateForm'])->name('session.createForm');
    Route::post('/create-session', [SessionController::class, 'create'])->name('session.create');
    Route::get('/join-session', [SessionController::class, 'join'])->name('session.join');
    Route::post('/join-session', [SessionController::class, 'store'])->name('session.store');
    Route::get('/movies.json', [MovieController::class, 'getMoviesJson']);
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::post('/update-settings', [UserController::class, 'updateSettings'])->name('update-settings');


    Route::prefix('/session/{session}')->group(function () {
        Route::get('/', [SessionController::class, 'show'])->name('session.show');
        Route::delete('/', [SessionController::class, 'destroy'])->name('session.destroy');
        Route::post('/leave', [SessionController::class, 'leave'])->name('session.leave');
        Route::get('/users', [SessionController::class, 'getUsers'])->name('session.users');
        Route::get('/movies', [MovieController::class, 'index'])->name('movie.index');
        Route::post('/like/{kinopoiskId}', [MovieController::class, 'like'])->name('movie.like');
        Route::post('/dislike/{kinopoiskId}', [MovieController::class, 'dislike'])->name('movie.dislike');
        Route::post('/react', [MovieController::class, 'react'])->name('movie.react');
        Route::get('/check', [MovieController::class, 'check'])->name('movie.check');
        Route::get('/liked-movies', [SessionController::class, 'getLikedMoviesInSession'])->name('session.likedMoviesInSession');
    });
});

require __DIR__ . '/auth.php';

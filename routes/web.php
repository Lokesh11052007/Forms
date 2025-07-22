<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialLoginController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', fn () => redirect()->route('login'));

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Login
Route::get('/auth/google', [SocialLoginController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialLoginController::class, 'callback'])->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard and form builder
    Route::get('/dashboard', [FormController::class, 'dashboard'])->name('dashboard');
    Route::get('/build-form', [FormController::class, 'builder'])->name('form.builder');
    Route::post('/forms', [FormController::class, 'store'])->name('form.store');
    Route::delete('/forms/{id}', [FormController::class, 'deleteForm'])->name('form.delete');
    Route::get('/forms/{id}/responses', [FormController::class, 'viewResponses'])->name('form.responses');
});

/*
|--------------------------------------------------------------------------
| Public Routes (Form Views and Submissions)
|--------------------------------------------------------------------------
*/
Route::get('/forms/{id}', [FormController::class, 'showForm'])->name('form.view');
Route::post('/forms/submit', [FormController::class, 'storeResponse'])->name('form.response.submit');

Route::get('/form/respond', [FormController::class, 'respondViaUrl'])->name('form.respond.url');

// Catch-all for 404
Route::fallback(fn () => response('404 | Page not found', 404));

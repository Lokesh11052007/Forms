<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialLoginController;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\AuthController;

// Show the registration form
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');

// Handle registration form submission
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Show login form
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');

// Handle login
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


use App\Http\Controllers\FormController;

Route::middleware('auth')->group(function () {
    Route::get('/form-builder', [FormController::class, 'showBuilder'])->name('form.builder');
    Route::post('/form-builder', [FormController::class, 'storeField'])->name('form.store');
    Route::get('/form-preview', [FormController::class, 'previewForm'])->name('form.preview');
    Route::get('/dashboard/forms/{form}', [FormController::class, 'viewResponses'])->name('form.responses');
});



Route::get('/dashboard', [FormController::class, 'dashboard'])->name('dashboard')->middleware('auth');


Route::get('/forms/{username}', [FormController::class, 'publicForm'])->name('forms.public');



Route::get('/auth/google', [SocialLoginController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialLoginController::class, 'callback']);

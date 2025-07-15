<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\SocialLoginController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Social Login
Route::get('/auth/google', [SocialLoginController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialLoginController::class, 'callback']);

// Authenticated Form Routes
Route::middleware('auth')->group(function () {
    Route::get('/form-builder', [FormController::class, 'showBuilder'])->name('form.builder');
    Route::post('/form-builder', [FormController::class, 'storeField'])->name('form.storeField');
    Route::post('/form/store', [FormController::class, 'store'])->name('form.store');
    Route::get('/form-preview', [FormController::class, 'previewForm'])->name('form.preview');
    Route::get('/dashboard/forms/{form}', [FormController::class, 'viewResponses'])->name('form.responses');
});

Route::get('/dashboard', [FormController::class, 'dashboard'])->name('dashboard')->middleware('auth');

// ✅ PUBLIC FORM ROUTES
Route::get('/forms/{username}/{slug}', [FormController::class, 'showResponseForm'])->name('form.fill');
Route::post('/forms/{username}/{slug}', [FormController::class, 'submitResponse'])->name('form.submit');

// ❌ REMOVE THIS if unused or conflicting
// Route::get('/forms/{username}/{form}', [FormController::class, 'publicForm'])->name('forms.public');

Route::delete('/form/delete/{form}', [FormController::class, 'deleteForm'])->name('form.delete');

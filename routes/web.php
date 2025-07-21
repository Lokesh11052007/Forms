<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| 🔐 Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 🌐 Social Login (Google)
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [SocialLoginController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialLoginController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| 🛠️ Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Form Builder (Standard)
    Route::get('/form-builder', [FormController::class, 'showBuilder'])->name('form.builder');
    Route::post('/form-builder', [FormController::class, 'storeField'])->name('form.storeField');
    Route::get('/form-preview', [FormController::class, 'previewForm'])->name('form.preview');

    // View Responses (Dashboard)
    Route::get('/dashboard/forms/{form}', [FormController::class, 'viewResponses'])->name('form.responses');

    // Delete Form
    Route::delete('/form/delete/{form}', [FormController::class, 'deleteForm'])->name('form.delete');
});

/*
|--------------------------------------------------------------------------
| 🧱 JSON-Based Form Builder (Public Builder Interface)
|--------------------------------------------------------------------------
*/
Route::get('/build-form', [FormController::class, 'builder'])->name('form.builder.json');
Route::post('/form/store', [FormController::class, 'store'])->name('form.store');

/*
|--------------------------------------------------------------------------
| 📥 Public Form Submissions (JSON-Based)
|--------------------------------------------------------------------------
| This is the route for forms using ?data= query (dynamic JSON forms)
|--------------------------------------------------------------------------
*/
Route::get('/form/respond', [FormController::class, 'respondViaUrl'])->name('form.respond.url');
Route::post('/form/respond', [FormController::class, 'storeResponse'])->name('form.respond');

// 🔁 This POST may be optional if you're not using it separately
Route::post('/form/respond/submit', [FormController::class, 'storeResponse'])->name('form.response.submit');

/*
|--------------------------------------------------------------------------
| 🌐 Public Fillable Form Routes (User-slug Based)
|--------------------------------------------------------------------------
*/
Route::get('/forms/{username}/{slug}', [FormController::class, 'showResponseForm'])->name('form.fill');
Route::post('/forms/{username}/{slug}', [FormController::class, 'submitResponse'])->name('form.submit');

/*
|--------------------------------------------------------------------------
| 📤 Export Responses (Admin Only)
|--------------------------------------------------------------------------
*/
Route::get('/admin/responses', [FormController::class, 'viewResponses'])->name('form.responses.index');
Route::get('/admin/responses/export/excel', [FormController::class, 'exportExcel'])->name('form.responses.export.excel');
Route::get('/admin/responses/export/pdf', [FormController::class, 'exportPdf'])->name('form.responses.export.pdf');

/*
|--------------------------------------------------------------------------
| 🧪 Debug Route - Show All Tables
|--------------------------------------------------------------------------
*/
Route::get('/debug/show-tables', function () {
    $tables = DB::select('SHOW TABLES');

    echo "<h2>📋 Tables in Database:</h2>";
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        echo "<div style='padding: 5px 0; border-bottom: 1px solid #ccc;'>$tableName</div>";
    }
});

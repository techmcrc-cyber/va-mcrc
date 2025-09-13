<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Admin\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// Welcome/Landing Page (Public)
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Authentication Routes (Public)
Route::middleware('guest:web')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('logout', [LoginController::class, 'logout'])->name('admin.logout');

// Include admin routes
require __DIR__.'/admin.php';

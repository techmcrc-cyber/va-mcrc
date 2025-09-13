<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;

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

// Include admin routes
require __DIR__.'/admin.php';

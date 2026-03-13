<?php

declare(strict_types=1);

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\ContactView;
use Illuminate\Support\Facades\Route;

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

Route::redirect('/', 'auth/login');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/home', HomeController::class)->name('home');

    Route::post('/users', [UsersController::class, 'store'])->name('users.store');

    Route::resource('contact', ContactController::class)->middleware(ContactView::class);
});

include __DIR__.'/auth.php';
include __DIR__.'/my.php';

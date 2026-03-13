<?php

declare(strict_types=1);

use App\Http\Controllers\BankController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\BankView;
use App\Http\Middleware\ContactView;
use App\Http\Middleware\OutletView;
use App\Http\Middleware\ProductView;
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

    Route::resource('product', ProductController::class)->middleware(ProductView::class);

    Route::resource('outlet', OutletController::class)->middleware(OutletView::class);

    Route::resource('banks', BankController::class)->middleware(BankView::class);
});

include __DIR__.'/auth.php';
include __DIR__.'/my.php';

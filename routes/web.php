<?php

declare(strict_types=1);

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\BankAccountView;
use App\Http\Middleware\BankView;
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

    Route::middleware(BankView::class)->group(function (): void {
        Route::get('banks/export', [BankController::class, 'export'])->name('banks.export');
        Route::get('banks/template', [BankController::class, 'template'])->name('banks.template');
        Route::post('banks/import', [BankController::class, 'import'])->name('banks.import');
        Route::resource('banks', BankController::class);
    });

    Route::middleware(BankAccountView::class)->group(function (): void {
        Route::get('bank-accounts/export', [BankAccountController::class, 'export'])->name('bank-accounts.export');
        Route::post('bank-accounts/import', [BankAccountController::class, 'import'])->name('bank-accounts.import');
        Route::get('bank-accounts/template', [BankAccountController::class, 'template'])->name('bank-accounts.template');
        Route::resource('bank-accounts', BankAccountController::class);
    });

    Route::middleware(ProductView::class)->group(function (): void {
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
        Route::get('products/template', [ProductController::class, 'template'])->name('products.template');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::resource('products', ProductController::class);
    });
});

include __DIR__.'/auth.php';
include __DIR__.'/my.php';

<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UsersController;
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

    Route::middleware(ProductView::class)->group(function (): void {
        Route::get('product/export', [ProductController::class, 'export'])->name('product.export');
        Route::get('product/template', [ProductController::class, 'template'])->name('product.template');
        Route::post('product/import', [ProductController::class, 'import'])->name('product.import');
        Route::resource('product', ProductController::class);
    });
});

include __DIR__.'/auth.php';
include __DIR__.'/my.php';

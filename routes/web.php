<?php

// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', function () {
    return view('pages.home');
});
use App\Http\Controllers\Api\CartOrderApiController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\OrderController;

Route::prefix('api/cart')->group(function () {
    Route::get('/', [CartOrderApiController::class, 'getCart']);
    Route::post('/', [CartOrderApiController::class, 'addToCart']);
    Route::put('/{productId}', [CartOrderApiController::class, 'updateCart']);
    Route::delete('/{productId}', [CartOrderApiController::class, 'removeFromCart']);
});

Route::post('/api/orders', [OrderController::class, 'store'])->name('api.orders.store');

Route::middleware(['auth', 'role:sales,admin'])
    ->patch('/api/orders/{id}/status', [CartOrderApiController::class, 'updateOrderStatus']);

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::post('/orders/{orderId}/complaints', [ComplaintController::class, 'store'])
        ->name('complaints.store');
});

Route::middleware(['auth', 'role:sales,admin'])->prefix('sales')->group(function () {
    Route::get('/orders', [ComplaintController::class, 'salesIndex'])
        ->name('sales.orders');
    Route::post('/complaints/{complaintId}/resolve', [ComplaintController::class, 'resolve'])
        ->name('sales.complaints.resolve');
});
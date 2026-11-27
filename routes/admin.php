<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BatchDisposalController;


use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(EnsureAdmin::class)
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ==============================
        // QUẢN LÝ LÔ HÀNG
        // ==============================

        // Danh sách lô
        Route::get('/batches', [BatchController::class, 'index'])
            ->name('batches.index');

        // Tạo lô
        Route::get('/batches/create', [BatchController::class, 'create'])
            ->name('batches.create');

        Route::post('/batches', [BatchController::class, 'store'])
            ->name('batches.store');

        // Sửa lô
        Route::get('/batches/{batch}/edit', [BatchController::class, 'edit'])
            ->name('batches.edit');

        Route::put('/batches/{batch}', [BatchController::class, 'update'])
            ->name('batches.update');

        // Xóa lô
        Route::delete('/batches/{batch}', [BatchController::class, 'destroy'])
            ->name('batches.destroy');

        // Chi tiết lô
        Route::get('/batches/{batch}', [BatchController::class, 'show'])
            ->name('batches.show');

        // ==============================
        // XUẤT HỦY LÔ HÀNG
        // ==============================

        Route::get('/disposals/create', [BatchDisposalController::class, 'create'])
            ->name('disposals.create');

        Route::post('/disposals', [BatchDisposalController::class, 'store'])
            ->name('disposals.store');

        // ==============================
        // CẤU HÌNH CẢNH BÁO CẬN DATE
        // ==============================

        Route::post('/batches/alert-days', [BatchController::class, 'updateAlertDays'])
            ->name('batches.alert-days.update');

        // ==============================
        // SẢN PHẨM
        // ==============================

        Route::resource('products', ProductController::class)
            ->except('show');

        // ==============================
        // ĐƠN HÀNG
        // ==============================

        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders.index');

        Route::post('/orders/{order}/delivered', [OrderController::class, 'markDelivered'])
            ->name('orders.delivered');

        // ==============================
        // KHÁCH HÀNG
        // ==============================

        Route::get('/customers', [CustomerController::class, 'index'])
            ->name('customers.index');
    });

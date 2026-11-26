<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Middleware\EnsureAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(EnsureAdmin::class)->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/{batch}', [BatchController::class, 'show'])->name('batches.show');

    Route::resource('products', ProductController::class)->except('show');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/delivered', [OrderController::class, 'markDelivered'])->name('orders.delivered');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
});

// CHỈ DÙNG KHI TEST LOCAL. XÓA trước khi triển khai (Tuần 7).
if (app()->environment('local')) {
    Route::get('/dev-login/{role}', function (string $role) {
        $user = User::where('role', $role)->firstOrFail();
        Auth::login($user);

        return redirect()->route('admin.dashboard');
    })->name('dev.login');

    Route::get('/dev-logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('dev.logout');
}


<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Api\CartOrderApiController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\OrderController;

// Trang chủ
Route::get('/', function () {
    $products = Product::orderByDesc('id')->take(6)->get();

    return view('pages.home', compact('products'));
});

// Trang giỏ hàng
Route::get('/cart', function () {
    return view('cart.index');
})->name('cart.index');

// Trang thanh toán COD
Route::get('/cart/checkout', function () {
    return view('cart.checkout');
})->name('cart.checkout');

// Trang đặt hàng thành công
Route::get('/cart/thank-you', function () {
    return view('cart.thank-you');
})->name('cart.thank-you');

// API giỏ hàng
Route::prefix('api/cart')->group(function () {
    Route::get('/', [CartOrderApiController::class, 'getCart']);
    Route::post('/', [CartOrderApiController::class, 'addToCart']);
    Route::put('/{productId}', [CartOrderApiController::class, 'updateCart']);
    Route::delete('/{productId}', [CartOrderApiController::class, 'removeFromCart']);
});

// API đặt hàng COD
Route::post('/api/orders', [OrderController::class, 'store'])
    ->name('api.orders.store');

// Cập nhật trạng thái đơn hàng
Route::middleware(['auth', 'role:sales,admin'])
    ->patch('/api/orders/{id}/status', [CartOrderApiController::class, 'updateOrderStatus']);

// Khiếu nại đơn hàng
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::post('/orders/{orderId}/complaints', [ComplaintController::class, 'store'])
        ->name('complaints.store');
});

// Quản lý đơn hàng và khiếu nại
Route::middleware(['auth', 'role:sales,admin'])->prefix('sales')->group(function () {
    Route::get('/orders', [ComplaintController::class, 'salesIndex'])
        ->name('sales.orders');

    Route::post('/complaints/{complaintId}/resolve', [ComplaintController::class, 'resolve'])
        ->name('sales.complaints.resolve');
});

// Quản lý lô hàng
Route::middleware('auth')->group(function () {
    Route::resource('batches', BatchController::class)->except('show');

    Route::post('batches-warning', [BatchController::class, 'updateWarning'])
        ->name('batches.warning');
});

// Làm mới CSRF token
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

// Đăng nhập lại khi phiên hết hạn
Route::post('/relogin', function (Request $request) {
    $cred = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($cred)) {
        $request->session()->regenerate();

        return response()->json(['token' => csrf_token()]);
    }

    return response()->json(['message' => 'Sai thông tin'], 422);
});

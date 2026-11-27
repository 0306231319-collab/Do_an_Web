
<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Models\Batch;
use App\Http\Controllers\Api\CartOrderApiController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TraceabilityController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutVerifyController;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ProfileController;

// Trang chủ
Route::get('/', function () {
    $products = Product::orderByDesc('id')->take(6)->get();

    return view('pages.home', compact('products'));
});

// Trang Về chúng tôi
Route::view('/ve-chung-toi', 'pages.about')->name('about');

// Thông tin cá nhân của người dùng đã đăng nhập
Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan', [ProfileController::class, 'show'])->name('profile');
    Route::put('/tai-khoan', [ProfileController::class, 'update'])->name('profile.update');
});

// Gói định kỳ bản rút gọn
Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');

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
    Route::get('/orders/{orderId}/complaints/create', function (\Illuminate\Http\Request $request, int $orderId) {
        $order = \App\Models\Order::where('id', $orderId)->where('user_id', $request->user()->id)->firstOrFail();
        return view('orders.complaint_page', compact('order'));
    })->name('complaints.create');

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

// Tuần 5: Trang truy xuất nguồn gốc (công khai khi quét mã QR)
Route::get('/truy-xuat/{batch_code}', [TraceabilityController::class, 'show'])
    ->name('traceability.show');

// Tuần 5: Nghiệp vụ dành cho Farm Staff (sinh mã QR & nhập nhật ký canh tác)
Route::middleware(['auth', CheckRole::class . ':Farm Staff'])->prefix('farm')->group(function () {
    Route::post('/batches/{batch}/qr', [TraceabilityController::class, 'generateQr'])
        ->name('farm.batches.qr');
    Route::post('/batches/{batch}/farming-logs', [TraceabilityController::class, 'storeLog'])
        ->name('farm.batches.logs.store');
});

// Tuần 6: Danh sách/tìm kiếm/lọc sản phẩm, chi tiết sản phẩm, giới thiệu nông trại
Route::get('/san-pham', [CatalogController::class, 'index'])->name('products.index');
Route::get('/san-pham/{product}', [CatalogController::class, 'show'])->name('products.show');
Route::get('/gioi-thieu-nong-trai', [CatalogController::class, 'aboutFarm'])->name('farm.about');

// Tuần 7: API kiểm tra & tính tổng tiền phía Backend (chống sửa giá bằng F12)
Route::post('/api/checkout/verify-total', [CheckoutVerifyController::class, 'verifyCartTotal'])
    ->name('api.checkout.verify');

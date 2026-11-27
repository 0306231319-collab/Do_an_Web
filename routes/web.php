<?php

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

// Trang chủ
// Trang chủ -> Mở thẳng giao diện Mockup 1 (Lô hàng và hao hụt)
Route::get('/', function () {
    return view('pages.farm_batches');
});

Route::get('/farm/batches', function () {
    $latestBatchCode = Batch::latest()->value('batch_code');
    return view('pages.farm_batches', compact('latestBatchCode'));
})->name('farm.batches.index');

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

// Tuần 4: Giao diện theo Figma (Mockup 1 & Mockup 2)
Route::get('/farm/batches', function () {
    $latestBatchCode = Batch::latest()->value('batch_code');
    return view('pages.farm_batches', compact('latestBatchCode'));
})->name('farm.batches.index');

Route::get('/farm/batches/{id}/dispose', function ($id) {
    return view('pages.farm_dispose');
})->name('farm.batches.dispose');

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

// Trang đăng nhập & xử lý đăng nhập (cho mục Tài khoản)
Route::get('/login', function () {
    return view('pages.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ], [
        'email.required'    => 'Vui lòng nhập email.',
        'email.email'       => 'Email không đúng định dạng.',
        'password.required' => 'Vui lòng nhập mật khẩu.',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/farm/batches')->with('success', 'Đăng nhập thành công!');
    }

    return back()->withErrors([
        'email' => 'Email hoặc mật khẩu không chính xác.',
    ])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login')->with('success', 'Đã đăng xuất tài khoản.');
})->name('logout');
<?php

namespace App\Http\Controllers;

use App\Models\DeliverySchedule;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();
        $userId = auth()->id() ?? User::query()->value('id');

        $subscriptions = Subscription::with(['items.product', 'schedules.order'])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->orderByDesc('id')
            ->get();

        return view('subscriptions.index', compact('products', 'subscriptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_goi'      => 'required|string|max:150',
            'nguoi_nhan'   => 'required|string|max:100',
            'sdt_nhan'     => 'required|string|max:15',
            'dia_chi_giao' => 'required|string|max:500',
            'ngay_bat_dau' => 'required|date|after_or_equal:today',
            'product_id'   => 'required|integer|exists:products,id',
            'so_luong'     => 'required|numeric|min:0.5',
        ]);

        $userId = auth()->id() ?? User::query()->value('id');

        DB::transaction(function () use ($validated, $userId) {
            $product = Product::findOrFail($validated['product_id']);
            $unitPrice = (float) ($product->discount_price ?? $product->base_price);
            $soKyGiao = 4; // Gói 1 tháng = 4 tuần (4 kỳ giao)

            // Backend tự tính tổng tiền gói 1 tháng (4 kỳ)
            $tongTien = round($unitPrice * (float) $validated['so_luong'] * $soKyGiao, 2);

            $ngayBatDau = Carbon::parse($validated['ngay_bat_dau']);
            $ngayKetThuc = $ngayBatDau->copy()->addDays(28);

            // 1. Tạo gói định kỳ (thanh toán mô phỏng -> DangHoatDong)
            $subscription = Subscription::create([
                'user_id'       => $userId,
                'ten_goi'       => $validated['ten_goi'],
                'nguoi_nhan'    => $validated['nguoi_nhan'],
                'sdt_nhan'      => $validated['sdt_nhan'],
                'dia_chi_giao'  => $validated['dia_chi_giao'],
                'tong_tien'     => $tongTien,
                'ngay_bat_dau'  => $ngayBatDau->toDateString(),
                'ngay_ket_thuc' => $ngayKetThuc->toDateString(),
                'trang_thai'    => 'DangHoatDong',
            ]);

            // 2. Lưu sản phẩm đăng ký trong gói
            SubscriptionItem::create([
                'subscription_id' => $subscription->id,
                'product_id'      => $product->id,
                'so_luong'        => $validated['so_luong'],
            ]);

            // 3. Tự động tạo 4 dòng lịch giao hàng (mỗi 7 ngày)
            for ($i = 0; $i < $soKyGiao; $i++) {
                DeliverySchedule::create([
                    'subscription_id'     => $subscription->id,
                    'ngay_giao_du_kien'   => $ngayBatDau->copy()->addDays($i * 7)->toDateString(),
                    'trang_thai_sinh_don' => 'ChoTaoDon',
                    'ghi_chu'             => 'Kỳ giao số ' . ($i + 1) . '/' . $soKyGiao,
                ]);
            }
        });

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Đăng ký gói định kỳ 1 tháng thành công và đã khởi tạo lịch giao 4 kỳ!');
    }
}
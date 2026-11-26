<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\FefoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        private readonly FefoService $fefoService
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nguoi_nhan'        => 'required|string|max:255',
            'sdt_nhan'          => 'required|string|max:20',
            'dia_chi_giao'      => 'required|string|max:500',
            'ghi_chu'           => 'nullable|string|max:500',
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|integer|exists:products,id',
            'items.*.quantity'  => 'required|numeric|min:0.01',
        ]);

        // Lấy user đang đăng nhập, hoặc lấy user mặc định đầu tiên khi test Postman
        $userId = auth()->id() ?? User::query()->value('id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'code'    => 422,
                'message' => 'Hệ thống chưa có tài khoản người dùng mẫu trong database.',
            ], 422);
        }

        try {
            $order = DB::transaction(function () use ($validated, $userId) {
                $maDonHang = 'DH' . now()->format('YmdHis') . strtoupper(Str::random(4));

                // 1. Tạo đơn hàng ban đầu (giá tạm tính = 0, backend sẽ cộng dồn chuẩn xác bên dưới)
                $order = Order::create([
                    'user_id'        => $userId,
                    'ma_don_hang'    => $maDonHang,
                    'loai_don'       => 'Thuong',
                    'nguoi_nhan'     => $validated['nguoi_nhan'],
                    'sdt_nhan'       => $validated['sdt_nhan'],
                    'dia_chi_giao'   => $validated['dia_chi_giao'],
                    'ghi_chu'        => $validated['ghi_chu'] ?? null,
                    'tam_tinh'       => 0,
                    'phi_van_chuyen' => 0,
                    'tong_tien'      => 0,
                    'trang_thai'     => 'ChoDuyet',
                ]);

                $tamTinh = 0;

                // 2. Gọi FefoService trừ kho theo hạn gần nhất & tự tính tiền từ Backend
                foreach ($validated['items'] as $item) {
                    $details = $this->fefoService->exportForOrder(
                        $order,
                        (int) $item['product_id'],
                        (float) $item['quantity'],
                        (int) $userId
                    );

                    foreach ($details as $detail) {
                        $tamTinh += (float) $detail->thanh_tien;
                    }
                }

                // 3. Cập nhật lại tổng tiền chuẩn xác từ Backend
                $order->tam_tinh  = round($tamTinh, 2);
                $order->tong_tien = round($tamTinh + (float) $order->phi_van_chuyen, 2);
                $order->save();

                return $order->load('details.batch');
            });

            return response()->json([
                'success' => true,
                'code'    => 201,
                'message' => 'Đặt hàng thành công.',
                'data'    => $order,
            ], 201);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'code'    => 422,
                'message' => $e->getMessage(),
                'errors'  => null,
            ], 422);
        }
    }
}
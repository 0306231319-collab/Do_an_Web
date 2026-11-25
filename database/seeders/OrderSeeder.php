<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SystemConfig;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    private int $seq = 0;

    public function run(): void
    {
        $khach1 = User::where('email', 'khach1@nongsan.test')->first();
        $khach2 = User::where('email', 'khach2@nongsan.test')->first();
        $sales = User::where('role', User::ROLE_SALES)->first();

        // Đơn đã giao 3 giờ trước: còn trong hạn 24 giờ -> dùng thử khiếu nại hợp lệ
        $this->delivered($khach1, $sales, now()->subHours(3), [['RM-01', 2]], 15000);
        // Đơn đã giao 2 ngày trước: quá 24 giờ -> dùng thử từ chối khiếu nại tự động
        $this->delivered($khach2, $sales, now()->subDays(2), [['CR-01', 1]], 15000);
        // Đơn chờ duyệt: chưa gán lô, dùng thử FEFO khi Sales duyệt (10 kg cà chua Beef)
        $this->pending($khach1, [['TC-BEEF-01', 10]], 20000);
    }

    private function newCode(): string
    {
        $this->seq++;

        return 'DH' . now()->format('Ymd') . str_pad((string) $this->seq, 3, '0', STR_PAD_LEFT);
    }

    private function delivered(User $customer, User $sales, Carbon $deliveredAt, array $lines, int $ship): void
    {
        $order = Order::create([
            'user_id' => $customer->id,
            'ma_don_hang' => $this->newCode(),
            'loai_don' => 'Thuong',
            'nguoi_nhan' => $customer->full_name,
            'sdt_nhan' => $customer->phone,
            'dia_chi_giao' => '123 Đường mẫu, TP. Hồ Chí Minh',
            'phi_van_chuyen' => $ship,
            'trang_thai' => 'DaGiao',
            'ngay_giao_thuc_te' => $deliveredAt,
        ]);

        $subtotal = 0;
        foreach ($lines as [$sku, $qty]) {
            $product = Product::where('sku', $sku)->firstOrFail();
            // Lấy lô theo FEFO đơn giản (dữ liệu mẫu đủ hàng ở lô đầu tiên)
            $batch = $product->availableBatchesFefo()->first();
            $amount = $qty * (float) $product->base_price;
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'batch_id' => $batch->id,
                'so_luong' => $qty,
                'don_gia' => $product->base_price,
                'thanh_tien' => $amount,
            ]);
            $this->deduct($batch, $qty, $sales, $order);
            $subtotal += $amount;
        }

        $order->update(['tam_tinh' => $subtotal, 'tong_tien' => $subtotal + $ship]);

        Payment::create([
            'order_id' => $order->id,
            'phuong_thuc' => 'COD',
            'so_tien' => $subtotal + $ship,
            'trang_thai' => 'ThanhCong',
            'ngay_thanh_toan' => $deliveredAt,
        ]);
    }

    private function pending(User $customer, array $lines, int $ship): void
    {
        $order = Order::create([
            'user_id' => $customer->id,
            'ma_don_hang' => $this->newCode(),
            'loai_don' => 'Thuong',
            'nguoi_nhan' => $customer->full_name,
            'sdt_nhan' => $customer->phone,
            'dia_chi_giao' => '123 Đường mẫu, TP. Hồ Chí Minh',
            'phi_van_chuyen' => $ship,
            'trang_thai' => 'ChoDuyet',
        ]);

        $subtotal = 0;
        foreach ($lines as [$sku, $qty]) {
            $product = Product::where('sku', $sku)->firstOrFail();
            $amount = $qty * (float) $product->base_price;
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'batch_id' => null,
                'so_luong' => $qty,
                'don_gia' => $product->base_price,
                'thanh_tien' => $amount,
            ]);
            $subtotal += $amount;
        }

        $order->update(['tam_tinh' => $subtotal, 'tong_tien' => $subtotal + $ship]);

        Payment::create([
            'order_id' => $order->id,
            'phuong_thuc' => 'COD',
            'so_tien' => $subtotal + $ship,
            'trang_thai' => 'ChoThanhToan',
        ]);
    }

    private function deduct(Batch $batch, float $qty, User $by, Order $order): void
    {
        $alertDays = (int) SystemConfig::getValue('near_expiry_alert_days', 2);
        $left = (float) $batch->current_quantity - $qty;
        $batch->update([
            'current_quantity' => $left,
            'status' => BatchSeeder::status($batch->expiry_date, $left, $alertDays),
        ]);

        InventoryLog::create([
            'batch_id' => $batch->id,
            'user_id' => $by->id,
            'order_id' => $order->id,
            'action_type' => 'EXPORT_FEFO',
            'quantity_change' => -$qty,
            'balance_after' => $left,
            'reason' => 'Xuất kho theo FEFO cho đơn ' . $order->ma_don_hang . ' (dữ liệu mẫu).',
        ]);
    }
}

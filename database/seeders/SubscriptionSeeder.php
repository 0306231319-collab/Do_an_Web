<?php

namespace Database\Seeders;

use App\Models\DeliverySchedule;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'khach1@nongsan.test')->first();
        $start = Carbon::today();
        $end = $start->copy()->addMonth();

        $sub = Subscription::create([
            'user_id' => $customer->id,
            'ten_goi' => 'Gói rau củ hữu cơ 1 tháng',
            'nguoi_nhan' => $customer->full_name,
            'sdt_nhan' => $customer->phone,
            'dia_chi_giao' => '123 Đường mẫu, TP. Hồ Chí Minh',
            'tong_tien' => 400000,
            'ngay_bat_dau' => $start->toDateString(),
            'ngay_ket_thuc' => $end->toDateString(),
            'trang_thai' => 'DangHoatDong',
        ]);

        foreach ([['RM-01', 1], ['CR-01', 1]] as [$sku, $qty]) {
            SubscriptionItem::create([
                'subscription_id' => $sub->id,
                'product_id' => Product::where('sku', $sku)->value('id'),
                'so_luong' => $qty,
            ]);
        }

        // Lịch giao thứ Ba và thứ Sáu hằng tuần trong thời hạn gói
        for ($day = $start->copy()->addDay(); $day->lte($end); $day->addDay()) {
            if (in_array($day->dayOfWeek, [Carbon::TUESDAY, Carbon::FRIDAY], true)) {
                DeliverySchedule::create([
                    'subscription_id' => $sub->id,
                    'ngay_giao_du_kien' => $day->toDateString(),
                    'trang_thai_sinh_don' => 'ChoTaoDon',
                ]);
            }
        }

        // Thanh toán trước cả gói (mô phỏng)
        Payment::create([
            'subscription_id' => $sub->id,
            'phuong_thuc' => 'COD',
            'so_tien' => 400000,
            'trang_thai' => 'ThanhCong',
            'ngay_thanh_toan' => now(),
        ]);
    }
}

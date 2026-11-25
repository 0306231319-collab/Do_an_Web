<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\FarmingLog;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\SystemConfig;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BatchSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::where('role', User::ROLE_FARM_STAFF)->first();
        $alertDays = (int) SystemConfig::getValue('near_expiry_alert_days', 2);

        // [sku, thu hoạch cách đây (ngày), hết hạn sau (ngày, âm là đã quá hạn), số lượng nhập, tồn hiện tại]
        // 15 lô: có lô sắp hết hạn, lô quá hạn, lô tồn bằng 0 để kiểm thử FEFO
        $lots = [
            ['TC-BEEF-01', 5, 2, 3, 3],    // lô A: sắp hết hạn, còn 3 kg (đặt 10 kg -> lấy 3 kg ở đây)
            ['TC-BEEF-01', 2, 5, 20, 20],  // lô B: lấy tiếp 7 kg
            ['TC-BEEF-01', 10, -1, 10, 10], // đã quá hạn -> FEFO phải bỏ qua
            ['TC-BEEF-01', 1, 10, 15, 0],  // tồn bằng 0 -> FEFO phải bỏ qua
            ['TC-BI-01', 3, 3, 5, 5],      // cà chua bi tổng 8 kg -> đặt 10 kg phải báo lỗi thiếu kho
            ['TC-BI-01', 2, 6, 3, 3],
            ['RM-01', 2, 1, 30, 30],
            ['RM-01', 1, 3, 50, 50],
            ['XL-01', 3, 2, 12, 12],
            ['XL-01', 1, 6, 20, 20],
            ['CR-01', 4, 7, 40, 40],
            ['CR-01', 2, 14, 60, 60],
            ['CR-01', 1, 20, 50, 50],
            ['DT-01', 2, 3, 10, 10],
            ['DT-01', 8, -2, 5, 5],
        ];

        $today = Carbon::today();
        foreach ($lots as $i => [$sku, $harvestAgo, $expiryIn, $initial, $current]) {
            $product = Product::where('sku', $sku)->firstOrFail();
            $harvest = $today->copy()->subDays($harvestAgo);
            $expiry = $today->copy()->addDays($expiryIn);
            $code = 'LOT-' . $harvest->format('Ymd') . '-' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);

            $batch = Batch::create([
                'product_id' => $product->id,
                'created_by' => $staff->id,
                'batch_code' => $code,
                'qr_code_url' => '/truy-xuat/' . $code,
                'harvest_date' => $harvest->toDateString(),
                'expiry_date' => $expiry->toDateString(),
                'initial_quantity' => $initial,
                'current_quantity' => $current,
                'status' => self::status($expiry, $current, $alertDays),
            ]);

            InventoryLog::create([
                'batch_id' => $batch->id,
                'user_id' => $staff->id,
                'action_type' => 'IMPORT',
                'quantity_change' => $initial,
                'balance_after' => $initial,
                'reason' => 'Nhập kho sau thu hoạch (dữ liệu mẫu).',
            ]);

            if ($current < $initial) {
                InventoryLog::create([
                    'batch_id' => $batch->id,
                    'user_id' => $staff->id,
                    'action_type' => 'DISPOSAL',
                    'quantity_change' => -($initial - $current),
                    'balance_after' => $current,
                    'reason' => 'Rau dập nát do lưu kho (dữ liệu mẫu).',
                ]);
            }

            // Nhật ký canh tác cho các lô cà chua Beef và rau muống
            if (in_array($sku, ['TC-BEEF-01', 'RM-01'], true)) {
                $steps = [['Gieo hạt', 60], ['Bón phân hữu cơ', 30], ['Tưới nước', 10], ['Thu hoạch', 0]];
                foreach ($steps as [$action, $daysBefore]) {
                    FarmingLog::create([
                        'batch_id' => $batch->id,
                        'farm_staff_id' => $staff->id,
                        'hanh_dong' => $action,
                        'mo_ta_chi_tiet' => $action . ' cho lô ' . $code . ' (dữ liệu mẫu).',
                        'thoi_gian' => $harvest->copy()->subDays($daysBefore)->setTime(7, 0),
                    ]);
                }
            }
        }
    }

    public static function status(Carbon $expiry, float $current, int $alertDays): string
    {
        $today = Carbon::today();
        if ($current <= 0) {
            return 'empty';
        }
        if ($expiry->lt($today)) {
            return 'expired';
        }
        if ($expiry->lte($today->copy()->addDays($alertDays))) {
            return 'near_expiry';
        }

        return 'available';
    }
}

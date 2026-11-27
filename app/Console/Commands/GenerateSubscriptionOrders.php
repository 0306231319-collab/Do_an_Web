<?php

namespace App\Console\Commands;

use App\Models\DeliverySchedule;
use App\Models\Order;
use App\Services\FefoService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class GenerateSubscriptionOrders extends Command
{
    protected $signature = 'subscriptions:generate-orders {--date= : Ngày giao dự kiến cần quét (YYYY-MM-DD), mặc định là ngày mai}';

    protected $description = 'Sinh đơn hàng con cho các lịch giao gói định kỳ trước ngày giao 01 ngày';

    public function __construct(
        private readonly FefoService $fefoService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        // Mặc định quét lịch giao vào ngày mai (trước ngày giao 01 ngày)
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : Carbon::tomorrow()->toDateString();

        $this->info("Đang kiểm tra lịch giao gói định kỳ cho ngày giao: {$targetDate}...");

        $schedules = DeliverySchedule::with(['subscription.items'])
            ->whereDate('ngay_giao_du_kien', $targetDate)
            ->where('trang_thai_sinh_don', 'ChoTaoDon')
            ->whereHas('subscription', fn ($q) => $q->where('trang_thai', 'DangHoatDong'))
            ->get();

        if ($schedules->isEmpty()) {
            $this->warn("Không có lịch giao nào ở trạng thái ChoTaoDon cho ngày {$targetDate}.");
            return self::SUCCESS;
        }

        foreach ($schedules as $schedule) {
            $sub = $schedule->subscription;

            try {
                $order = DB::transaction(function () use ($sub, $schedule) {
                    $maDonHang = 'DK' . now()->format('YmdHis') . strtoupper(Str::random(4));

                    // Tạo đơn con từ thông tin gói định kỳ
                    $order = Order::create([
                        'user_id'        => $sub->user_id,
                        'ma_don_hang'    => $maDonHang,
                        'loai_don'       => 'DinhKy',
                        'nguoi_nhan'     => $sub->nguoi_nhan,
                        'sdt_nhan'       => $sub->sdt_nhan,
                        'dia_chi_giao'   => $sub->dia_chi_giao,
                        'ghi_chu'        => "Đơn định kỳ từ gói #{$sub->id} ({$sub->ten_goi}) - Ngày giao {$schedule->ngay_giao_du_kien->format('d/m/Y')}",
                        'tam_tinh'       => 0,
                        'phi_van_chuyen' => 0,
                        'tong_tien'      => 0,
                        'trang_thai'     => 'ChoDuyet',
                    ]);

                    $tamTinh = 0;

                    foreach ($sub->items as $item) {
                        $details = $this->fefoService->exportForOrder(
                            $order,
                            (int) $item->product_id,
                            (float) $item->so_luong,
                            (int) $sub->user_id
                        );

                        foreach ($details as $detail) {
                            $tamTinh += (float) $detail->thanh_tien;
                        }
                    }

                    $order->tam_tinh  = round($tamTinh, 2);
                    $order->tong_tien = round($tamTinh, 2);
                    $order->save();

                    // Cập nhật lịch giao sang DaTaoDon
                    $schedule->update([
                        'order_id'            => $order->id,
                        'trang_thai_sinh_don' => 'DaTaoDon',
                        'ghi_chu'             => "Đã sinh đơn con {$order->ma_don_hang}",
                    ]);

                    return $order;
                });

                $this->info("[OK] Lịch #{$schedule->id} -> Đã tạo đơn con {$order->ma_don_hang} và trừ kho FEFO.");
            } catch (Throwable $e) {
                // Thiếu tồn kho -> transaction tạo đơn con tự rollback, đánh dấu lịch giao ThieuHang
                $schedule->update([
                    'trang_thai_sinh_don' => 'ThieuHang',
                    'ghi_chu'             => 'Thiếu hàng khi sinh đơn: ' . $e->getMessage(),
                ]);

                $this->error("[ThieuHang] Lịch #{$schedule->id} (Gói #{$sub->id}) không đủ hàng: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
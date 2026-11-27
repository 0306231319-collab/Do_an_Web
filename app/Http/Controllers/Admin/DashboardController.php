<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\SystemConfig;

class DashboardController extends Controller
{
    public function index()
    {
        $alertDays = (int) SystemConfig::getValue('near_expiry_alert_days', 2);
        $today = now()->toDateString();
        $alertDate = now()->addDays($alertDays)->toDateString();

        $selling = Batch::where('current_quantity', '>', 0)->whereDate('expiry_date', '>=', $today);

        $stats = [
            'products' => Product::count(),
            'batches_selling' => (clone $selling)->count(),
            'near_expiry' => (clone $selling)->whereDate('expiry_date', '<=', $alertDate)->count(),
            'expired_stock' => Batch::where('current_quantity', '>', 0)->whereDate('expiry_date', '<', $today)->count(),
            'pending_orders' => Order::where('trang_thai', 'ChoDuyet')->count(),
        ];

        $alerts = Batch::with('product')
            ->where('current_quantity', '>', 0)
            ->whereDate('expiry_date', '<=', $alertDate)
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();

        // ===== Báo cáo thống kê (Could-have) =====
        // Doanh thu: chỉ tính đơn đã giao
        $revenue = (float) Order::where('trang_thai', 'DaGiao')->sum('tong_tien');

        // Số đơn theo trạng thái
        $statusLabels = [
            'ChoDuyet' => 'Chờ duyệt',
            'DaXacNhan' => 'Đã xác nhận',
            'DangGiao' => 'Đang giao',
            'DaGiao' => 'Đã giao',
            'DaHuy' => 'Đã hủy',
        ];
        $counts = Order::selectRaw('trang_thai, COUNT(*) as total')
            ->groupBy('trang_thai')
            ->pluck('total', 'trang_thai');
        $ordersByStatus = [];
        foreach ($statusLabels as $key => $label) {
            $ordersByStatus[$label] = (int) ($counts[$key] ?? 0);
        }

        // Tỷ lệ hao hụt = tổng xuất hủy / tổng nhập kho
        $imported = (float) InventoryLog::where('action_type', 'IMPORT')->sum('quantity_change');
        $disposed = abs((float) InventoryLog::where('action_type', 'DISPOSAL')->sum('quantity_change'));
        $exported = abs((float) InventoryLog::where('action_type', 'EXPORT_FEFO')->sum('quantity_change'));
        $lossRate = $imported > 0 ? round($disposed / $imported * 100, 1) : 0;

        // Doanh thu 7 ngày gần nhất (theo ngày giao thực tế)
        $revenue7 = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $revenue7[$day->format('d/m')] = (float) Order::where('trang_thai', 'DaGiao')
                ->whereDate('ngay_giao_thuc_te', $day->toDateString())
                ->sum('tong_tien');
        }

        $report = compact('revenue', 'ordersByStatus', 'imported', 'disposed', 'exported', 'lossRate', 'revenue7');

        return view('admin.dashboard', compact('stats', 'alerts', 'alertDays', 'report'));
    }
}

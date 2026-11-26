<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
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

        return view('admin.dashboard', compact('stats', 'alerts', 'alertDays'));
    }
}

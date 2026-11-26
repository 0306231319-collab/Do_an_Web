<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->withCount([
            'details',
            'details as unassigned_count' => fn ($q) => $q->whereNull('batch_id'),
        ])->orderByDesc('id');

        $status = (string) $request->input('trang_thai');
        if ($status !== '') {
            $query->where('trang_thai', $status);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders', 'status'));
    }

    /**
     * Chuyển đơn sang "Đã giao" để thử luồng tích điểm (OrderObserver sẽ cộng điểm cho khách).
     * Luồng thật do Sales xử lý; nút này giúp Admin kiểm tra và demo.
     */
    public function markDelivered(Order $order)
    {
        if (! in_array($order->trang_thai, ['DaXacNhan', 'DangGiao'], true)) {
            return back()->with('error', 'Chỉ đơn đã xác nhận hoặc đang giao mới chuyển được sang Đã giao.');
        }

        if ($order->details()->whereNull('batch_id')->exists()) {
            return back()->with('error', 'Đơn chưa được gán lô (chưa xuất kho theo FEFO) nên chưa thể giao.');
        }

        $before = (int) $order->user()->value('loyalty_points');
        $order->update(['trang_thai' => 'DaGiao', 'ngay_giao_thuc_te' => now()]);
        $after = (int) $order->user()->value('loyalty_points');

        return back()->with('success', "Đơn {$order->ma_don_hang} đã chuyển sang Đã giao. Điểm tích lũy của khách: +" . ($after - $before) . '.');
    }
}

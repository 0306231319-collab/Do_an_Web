<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function store(Request $request, int $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($order->trang_thai !== 'DaGiao') {
            return back()->withErrors([
                'order' => 'Chỉ được khiếu nại đơn hàng ở trạng thái DaGiao.',
            ]);
        }

        $deliveredTime = $order->ngay_giao_thuc_te
            ? Carbon::parse($order->ngay_giao_thuc_te)
            : Carbon::parse($order->ngay_tao);

        if ($deliveredTime->diffInHours(now()) > 24) {
            return back()->withErrors([
                'order' => 'Đã quá thời hạn 24 giờ kể từ khi nhận hàng để gửi khiếu nại.',
            ]);
        }

        $validated = $request->validate([
            'ly_do'    => 'required|string|min:10|max:1000',
            'hinh_anh' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ], [
            'hinh_anh.mimes' => 'Chỉ chấp nhận tệp hình ảnh định dạng .jpg hoặc .png.',
            'hinh_anh.max'   => 'Dung lượng ảnh không được vượt quá 5 MB.',
        ]);

        $path = $request->file('hinh_anh')->store('complaints', 'public');

        Complaint::create([
            'order_id'   => $order->id,
            'user_id'    => $request->user()->id,
            'ly_do'      => $validated['ly_do'],
            'hinh_anh'   => $path,
            'trang_thai' => 'ChoXuLy',
        ]);

        return redirect()->back()->with('success', 'Gửi yêu cầu khiếu nại thành công!');
    }

    public function salesIndex()
    {
        $orders = Order::with(['user', 'complaint'])->orderByDesc('ngay_tao')->paginate(15);

        return view('sales.orders_complaints', compact('orders'));
    }

    public function resolve(Request $request, int $complaintId)
    {
        $validated = $request->validate([
            'trang_thai' => 'required|in:HoanTien,GiaoBu,TuChoi',
            'ly_do_xu_ly' => 'required|string|min:5|max:1000',
        ], [
            'ly_do_xu_ly.required' => 'Nhân viên Sales bắt buộc phải nhập lý do xử lý/từ chối.',
        ]);

        $complaint = Complaint::findOrFail($complaintId);
        $complaint->update([
            'trang_thai'  => $validated['trang_thai'],
            'ly_do_xu_ly' => $validated['ly_do_xu_ly'],
            'xu_ly_boi'   => $request->user()->id,
        ]);

        return back()->with('success', 'Đã cập nhật kết quả xử lý khiếu nại.');
    }
}
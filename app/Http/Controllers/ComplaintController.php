<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /** Khách gửi khiếu nại cho đơn đã giao (trong 24 giờ). */
    public function store(Request $request, int $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($order->trang_thai !== 'DaGiao') {
            return back()->withErrors(['order' => 'Chỉ được khiếu nại đơn hàng ở trạng thái Đã giao.']);
        }

        if ($order->complaint()->exists()) {
            return back()->withErrors(['order' => 'Đơn hàng này đã có khiếu nại, mỗi đơn chỉ gửi một lần.']);
        }

        $deliveredTime = $order->ngay_giao_thuc_te
            ? Carbon::parse($order->ngay_giao_thuc_te)
            : Carbon::parse($order->ngay_tao);

        if ($deliveredTime->lt(now()->subHours(24))) {
            return back()->withErrors(['order' => 'Đã quá thời hạn 24 giờ kể từ khi nhận hàng để gửi khiếu nại.']);
        }

        $validated = $request->validate([
            'ly_do'    => 'required|in:DapNat,HuHong,Khac',
            'mo_ta'    => 'nullable|string|max:1000',
            'hinh_anh' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ], [
            'ly_do.required'    => 'Vui lòng chọn lý do khiếu nại.',
            'ly_do.in'          => 'Lý do khiếu nại không hợp lệ.',
            'hinh_anh.required' => 'Vui lòng đính kèm ảnh minh chứng.',
            'hinh_anh.mimes'    => 'Chỉ chấp nhận tệp hình ảnh định dạng .jpg hoặc .png.',
            'hinh_anh.max'      => 'Dung lượng ảnh không được vượt quá 5 MB.',
        ]);

        $path = $request->file('hinh_anh')->store('complaints', 'public');

        Complaint::create([
            'order_id'            => $order->id,
            'ly_do'               => $validated['ly_do'],
            'mo_ta'               => $validated['mo_ta'] ?? null,
            'hinh_anh_minh_chung' => $path,
            'trang_thai'          => 'ChoDuyet',
        ]);

        return back()->with('success', 'Gửi khiếu nại thành công. Nhân viên bán hàng sẽ xử lý trong thời gian sớm nhất.');
    }

    /** Sales xem danh sách đơn và khiếu nại. */
    public function salesIndex(Request $request)
    {
        $orders = Order::with(['user', 'complaint'])
            ->when($request->query('loc') === 'khieu-nai', fn ($q) => $q->has('complaint'))
            ->orderByDesc('ngay_tao')
            ->paginate(15)
            ->withQueryString();

        return view('sales.orders_complaints', compact('orders'));
    }

    /** Sales xử lý khiếu nại: hoàn tiền, giao bù hoặc từ chối (bắt buộc nhập lý do). */
    public function resolve(Request $request, int $complaintId)
    {
        $validated = $request->validate([
            'trang_thai'  => 'required|in:HoanTien,GiaoBu,TuChoi',
            'ly_do_xu_ly' => 'required|string|min:5|max:255',
        ], [
            'ly_do_xu_ly.required' => 'Nhân viên Sales bắt buộc phải nhập lý do xử lý hoặc từ chối.',
            'ly_do_xu_ly.min'      => 'Lý do xử lý cần tối thiểu 5 ký tự.',
        ]);

        $complaint = Complaint::findOrFail($complaintId);

        if ($complaint->trang_thai !== 'ChoDuyet') {
            return back()->withErrors(['complaint' => 'Khiếu nại này đã được xử lý trước đó.']);
        }

        $complaint->update([
            'trang_thai'    => $validated['trang_thai'],
            'ly_do_tu_choi' => $validated['ly_do_xu_ly'],
            'sales_id'      => $request->user()->id,
            'ngay_duyet'    => now(),
        ]);

        return back()->with('success', 'Đã cập nhật kết quả xử lý khiếu nại.');
    }
}

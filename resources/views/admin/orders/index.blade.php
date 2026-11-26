@extends('admin.layout')
@section('title', 'Đơn hàng')
@section('content')
<h1 class="text-2xl font-bold mb-1">Đơn hàng</h1>
<p class="text-sm text-slate-500 mb-4">Nút "Đã giao" dùng để thử tích điểm khi demo. Luồng duyệt đơn và giao hàng thật do Sales xử lý.</p>

<form method="GET" class="mb-4 flex gap-2">
    <select name="trang_thai" class="min-h-11 rounded-lg border border-stone-300 px-3">
        <option value="">Tất cả trạng thái</option>
        @foreach (['ChoDuyet' => 'Chờ duyệt', 'DaXacNhan' => 'Đã xác nhận', 'DangGiao' => 'Đang giao', 'DaGiao' => 'Đã giao', 'DaHuy' => 'Đã hủy'] as $v => $l)
            <option value="{{ $v }}" @selected($status === $v)>{{ $l }}</option>
        @endforeach
    </select>
    <button class="min-h-11 rounded-lg bg-slate-700 px-5 font-semibold text-white">Lọc</button>
</form>

<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-stone-100 text-left text-slate-600">
            <tr><th class="px-4 py-2">Mã đơn</th><th class="px-4 py-2">Khách hàng</th><th class="px-4 py-2">Loại</th><th class="px-4 py-2 text-right">Tạm tính</th><th class="px-4 py-2 text-right">Tổng tiền</th><th class="px-4 py-2">Trạng thái</th><th class="px-4 py-2">Lô</th><th class="px-4 py-2"></th></tr>
        </thead>
        <tbody>
        @php $st = ['ChoDuyet' => 'Chờ duyệt', 'DaXacNhan' => 'Đã xác nhận', 'DangGiao' => 'Đang giao', 'DaGiao' => 'Đã giao', 'DaHuy' => 'Đã hủy']; @endphp
        @forelse ($orders as $o)
            <tr class="border-t border-stone-100">
                <td class="px-4 py-2 font-mono">{{ $o->ma_don_hang }}</td>
                <td class="px-4 py-2">{{ $o->user->full_name }}</td>
                <td class="px-4 py-2">{{ $o->loai_don }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($o->tam_tinh, 0, ',', '.') }}đ</td>
                <td class="px-4 py-2 text-right">{{ number_format($o->tong_tien, 0, ',', '.') }}đ</td>
                <td class="px-4 py-2">{{ $st[$o->trang_thai] ?? $o->trang_thai }}</td>
                <td class="px-4 py-2">{{ $o->unassigned_count > 0 ? 'Chưa gán lô' : 'Đã gán lô' }}</td>
                <td class="px-4 py-2">
                    @if (in_array($o->trang_thai, ['DaXacNhan', 'DangGiao'], true))
                        <form method="POST" action="{{ route('admin.orders.delivered', $o) }}" onsubmit="return confirm('Chuyển đơn {{ $o->ma_don_hang }} sang Đã giao?');">
                            @csrf
                            <button class="rounded-lg border border-emerald-700 px-3 py-1 text-emerald-700 hover:bg-emerald-50">Đã giao</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500">Không có đơn.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection

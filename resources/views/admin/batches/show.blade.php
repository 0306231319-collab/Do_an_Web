@extends('admin.layout')
@section('title', 'Lô ' . $batch->batch_code)
@section('content')
<a href="{{ route('admin.batches.index') }}" class="text-sm text-emerald-700 underline">&larr; Danh sách lô</a>
<h1 class="text-2xl font-bold mt-2 mb-4">Lô {{ $batch->batch_code }} @include('admin.partials.expiry-badge')</h1>

<dl class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8 text-sm">
    @php
        $info = [
            ['Sản phẩm', $batch->product->name],
            ['Người tạo lô', $batch->creator->full_name ?? '—'],
            ['Ngày thu hoạch', $batch->harvest_date->format('d/m/Y')],
            ['Hạn sử dụng', $batch->expiry_date->format('d/m/Y')],
            ['Số lượng nhập', rtrim(rtrim(number_format($batch->initial_quantity, 2, ',', '.'), '0'), ',') . ' ' . $batch->product->unit],
            ['Tồn kho hiện tại', rtrim(rtrim(number_format($batch->current_quantity, 2, ',', '.'), '0'), ',') . ' ' . $batch->product->unit],
            ['Mã QR truy xuất', $batch->qr_code_url],
            ['Trạng thái lưu', $batch->status],
        ];
    @endphp
    @foreach ($info as [$k, $v])
        <div class="rounded-xl border border-stone-200 bg-white p-3"><dt class="text-slate-500">{{ $k }}</dt><dd class="font-semibold break-all">{{ $v }}</dd></div>
    @endforeach
</dl>

<h2 class="text-lg font-semibold mb-2">Lịch sử biến động tồn kho (lô bị trừ bởi đơn nào)</h2>
<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white mb-8">
    <table class="min-w-full text-sm">
        <thead class="bg-stone-100 text-left text-slate-600">
            <tr><th class="px-4 py-2">Thời điểm</th><th class="px-4 py-2">Loại</th><th class="px-4 py-2">Đơn hàng</th><th class="px-4 py-2 text-right">Thay đổi</th><th class="px-4 py-2 text-right">Tồn sau</th><th class="px-4 py-2">Người thực hiện</th><th class="px-4 py-2">Lý do</th></tr>
        </thead>
        <tbody>
        @php $labels = ['IMPORT' => 'Nhập kho', 'EXPORT_FEFO' => 'Xuất bán (FEFO)', 'DISPOSAL' => 'Xuất hủy']; @endphp
        @forelse ($logs as $log)
            <tr class="border-t border-stone-100">
                <td class="px-4 py-2 whitespace-nowrap">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-2">{{ $labels[$log->action_type] ?? $log->action_type }}</td>
                <td class="px-4 py-2 font-mono">{{ $log->order->ma_don_hang ?? '—' }}</td>
                <td class="px-4 py-2 text-right font-semibold {{ $log->quantity_change < 0 ? 'text-red-700' : 'text-emerald-700' }}">{{ $log->quantity_change > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($log->quantity_change, 2, ',', '.'), '0'), ',') }}</td>
                <td class="px-4 py-2 text-right">{{ rtrim(rtrim(number_format($log->balance_after, 2, ',', '.'), '0'), ',') }}</td>
                <td class="px-4 py-2">{{ $log->user->full_name ?? '—' }}</td>
                <td class="px-4 py-2">{{ $log->reason }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Chưa có biến động.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<h2 class="text-lg font-semibold mb-2">Nhật ký canh tác của lô</h2>
<ol class="space-y-2">
    @forelse ($farmingLogs as $f)
        <li class="rounded-xl border border-stone-200 bg-white p-3 text-sm">
            <span class="font-semibold">{{ $f->hanh_dong }}</span>
            <span class="text-slate-500">· {{ $f->thoi_gian->format('d/m/Y H:i') }}</span>
            @if ($f->mo_ta_chi_tiet)<div class="text-slate-600">{{ $f->mo_ta_chi_tiet }}</div>@endif
        </li>
    @empty
        <li class="text-slate-500 text-sm">Chưa có nhật ký.</li>
    @endforelse
</ol>
@endsection

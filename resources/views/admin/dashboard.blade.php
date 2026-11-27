@extends('admin.layout')
@section('title', 'Tổng quan')
@section('content')
<h1 class="text-2xl font-bold mb-5">Tổng quan</h1>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-8">
    @php
        $cards = [
            ['Sản phẩm', $stats['products'], 'text-slate-800', route('admin.products.index')],
            ['Lô đang bán', $stats['batches_selling'], 'text-slate-800', route('admin.batches.index', ['filter' => 'selling'])],
            ['Lô sắp hết hạn', $stats['near_expiry'], 'text-red-700', route('admin.batches.index', ['filter' => 'near'])],
            ['Lô quá hạn còn tồn', $stats['expired_stock'], 'text-red-700', route('admin.batches.index', ['filter' => 'expired'])],
            ['Đơn chờ duyệt', $stats['pending_orders'], 'text-amber-700', route('admin.orders.index', ['trang_thai' => 'ChoDuyet'])],
        ];
    @endphp
    @foreach ($cards as [$label, $value, $color, $url])
        <a href="{{ $url }}" class="block rounded-xl border border-stone-200 bg-white p-4 hover:shadow">
            <div class="text-3xl font-bold {{ $color }}">{{ $value }}</div>
            <div class="text-sm text-slate-500 mt-1">{{ $label }}</div>
        </a>
    @endforeach
</div>
{{-- ===== BÁO CÁO THỐNG KÊ ===== --}}
@php
    $fmt = fn ($n) => rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
    $max7 = max(array_values($report['revenue7']) ?: [0]) ?: 1;
    $maxStatus = max(array_values($report['ordersByStatus']) ?: [0]) ?: 1;
@endphp

<h2 class="text-lg font-semibold mb-3">Báo cáo thống kê</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    <div class="rounded-xl border border-stone-200 bg-white p-4">
        <div class="text-sm text-slate-500">Doanh thu (đơn đã giao)</div>
        <div class="mt-1 text-2xl font-bold text-emerald-700">{{ number_format($report['revenue'], 0, ',', '.') }}đ</div>
    </div>
    <div class="rounded-xl border border-stone-200 bg-white p-4">
        <div class="text-sm text-slate-500">Tổng nhập kho</div>
        <div class="mt-1 text-2xl font-bold text-slate-800">{{ $fmt($report['imported']) }}</div>
    </div>
    <div class="rounded-xl border border-stone-200 bg-white p-4">
        <div class="text-sm text-slate-500">Đã xuất bán (FEFO) / Xuất hủy</div>
        <div class="mt-1 text-2xl font-bold text-slate-800">{{ $fmt($report['exported']) }} <span class="text-base text-slate-400">/</span> <span class="text-red-700">{{ $fmt($report['disposed']) }}</span></div>
    </div>
    <div class="rounded-xl border border-stone-200 bg-white p-4">
        <div class="text-sm text-slate-500">Tỷ lệ hao hụt</div>
        <div class="mt-1 text-2xl font-bold {{ $report['lossRate'] > 10 ? 'text-red-700' : 'text-amber-700' }}">{{ $report['lossRate'] }}%</div>
        <div class="text-xs text-slate-400">= xuất hủy / tổng nhập</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-8">
    <div class="rounded-xl border border-stone-200 bg-white p-4">
        <div class="mb-3 text-sm font-semibold text-slate-700">Doanh thu 7 ngày gần nhất</div>
        <div class="flex h-40 items-end gap-2">
            @foreach ($report['revenue7'] as $day => $value)
                <div class="flex flex-1 flex-col items-center justify-end h-full">
                    <div class="text-[10px] text-slate-500 mb-1">{{ $value > 0 ? number_format($value / 1000, 0, ',', '.') . 'k' : '' }}</div>
                    <div class="w-full rounded-t bg-emerald-600" style="height: {{ max(2, $value / $max7 * 100) }}%"></div>
                    <div class="mt-1 text-[11px] text-slate-500">{{ $day }}</div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="rounded-xl border border-stone-200 bg-white p-4">
        <div class="mb-3 text-sm font-semibold text-slate-700">Số đơn theo trạng thái</div>
        <div class="space-y-2">
            @foreach ($report['ordersByStatus'] as $label => $count)
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-24 shrink-0 text-slate-600">{{ $label }}</div>
                    <div class="h-5 flex-1 rounded bg-stone-100">
                        <div class="h-5 rounded bg-emerald-600" style="width: {{ $count / $maxStatus * 100 }}%"></div>
                    </div>
                    <div class="w-8 text-right font-semibold">{{ $count }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<h2 class="text-lg font-semibold mb-3">Lô cần xử lý (còn từ {{ $alertDays }} ngày trở xuống hoặc đã quá hạn)</h2>
<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-stone-100 text-left text-slate-600">
            <tr><th class="px-4 py-2">Mã lô</th><th class="px-4 py-2">Sản phẩm</th><th class="px-4 py-2">Hạn sử dụng</th><th class="px-4 py-2 text-right">Tồn kho</th><th class="px-4 py-2">Tình trạng</th></tr>
        </thead>
        <tbody>
        @forelse ($alerts as $batch)
            <tr class="border-t border-stone-100">
                <td class="px-4 py-2 font-mono"><a class="text-emerald-700 underline" href="{{ route('admin.batches.show', $batch) }}">{{ $batch->batch_code }}</a></td>
                <td class="px-4 py-2">{{ $batch->product->name }}</td>
                <td class="px-4 py-2">{{ $batch->expiry_date->format('d/m/Y') }}</td>
                <td class="px-4 py-2 text-right">{{ rtrim(rtrim(number_format($batch->current_quantity, 2, ',', '.'), '0'), ',') }} {{ $batch->product->unit }}</td>
                <td class="px-4 py-2">@include('admin.partials.expiry-badge')</td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Không có lô nào cần xử lý.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

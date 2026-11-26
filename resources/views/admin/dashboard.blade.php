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

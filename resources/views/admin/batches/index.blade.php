@extends('admin.layout')
@section('title', 'Lô hàng và tồn kho')
@section('content')
<h1 class="text-2xl font-bold mb-1">Lô hàng và tồn kho</h1>
<p class="text-sm text-slate-500 mb-4">Xếp theo hạn sử dụng gần nhất (thứ tự FEFO). Bấm mã lô để xem lịch sử xuất kho.</p>

<form method="GET" class="mb-4 flex flex-col gap-2 sm:flex-row">
    <input type="text" name="q" value="{{ $keyword }}" placeholder="Tìm theo mã lô hoặc tên sản phẩm"
           class="min-h-11 flex-1 rounded-lg border border-stone-300 px-3">
    <select name="filter" class="min-h-11 rounded-lg border border-stone-300 px-3">
        @foreach (['' => 'Tất cả', 'selling' => 'Đang bán', 'near' => 'Sắp hết hạn', 'expired' => 'Quá hạn còn tồn', 'empty' => 'Hết hàng'] as $v => $l)
            <option value="{{ $v }}" @selected($filter === (string) $v)>{{ $l }}</option>
        @endforeach
    </select>
    <button class="min-h-11 rounded-lg bg-emerald-700 px-5 font-semibold text-white hover:bg-emerald-800">Lọc</button>
</form>

<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-stone-100 text-left text-slate-600">
            <tr>
                <th class="px-4 py-2">Mã lô</th><th class="px-4 py-2">Sản phẩm</th><th class="px-4 py-2">Thu hoạch</th>
                <th class="px-4 py-2">Hạn sử dụng</th><th class="px-4 py-2 text-right">Nhập</th><th class="px-4 py-2 text-right">Tồn</th><th class="px-4 py-2">Tình trạng</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($batches as $batch)
            <tr class="border-t border-stone-100">
                <td class="px-4 py-2 font-mono"><a class="text-emerald-700 underline" href="{{ route('admin.batches.show', $batch) }}">{{ $batch->batch_code }}</a></td>
                <td class="px-4 py-2">{{ $batch->product->name }}</td>
                <td class="px-4 py-2">{{ $batch->harvest_date->format('d/m/Y') }}</td>
                <td class="px-4 py-2">{{ $batch->expiry_date->format('d/m/Y') }}</td>
                <td class="px-4 py-2 text-right">{{ rtrim(rtrim(number_format($batch->initial_quantity, 2, ',', '.'), '0'), ',') }}</td>
                <td class="px-4 py-2 text-right font-semibold">{{ rtrim(rtrim(number_format($batch->current_quantity, 2, ',', '.'), '0'), ',') }} {{ $batch->product->unit }}</td>
                <td class="px-4 py-2">@include('admin.partials.expiry-badge')</td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Không có lô nào.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $batches->links() }}</div>
@endsection

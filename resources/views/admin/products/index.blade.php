@extends('admin.layout')
@section('title', 'Sản phẩm')
@section('content')
<div class="mb-4 flex items-center justify-between gap-3">
    <h1 class="text-2xl font-bold">Sản phẩm</h1>
    <a href="{{ route('admin.products.create') }}" class="flex min-h-11 items-center rounded-lg bg-emerald-700 px-5 font-semibold text-white hover:bg-emerald-800">+ Thêm sản phẩm</a>
</div>

<form method="GET" class="mb-4 flex flex-col gap-2 sm:flex-row">
    <input name="q" value="{{ $keyword }}" placeholder="Tìm theo tên hoặc SKU" class="min-h-11 flex-1 rounded-lg border border-stone-300 px-3">
    <select name="category_id" class="min-h-11 rounded-lg border border-stone-300 px-3">
        <option value="">Tất cả danh mục</option>
        @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <button class="min-h-11 rounded-lg bg-slate-700 px-5 font-semibold text-white">Lọc</button>
</form>

<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-stone-100 text-left text-slate-600">
            <tr><th class="px-4 py-2">Ảnh</th><th class="px-4 py-2">SKU</th><th class="px-4 py-2">Tên</th><th class="px-4 py-2">Danh mục</th><th class="px-4 py-2 text-right">Giá bán</th><th class="px-4 py-2 text-right">Số lô</th><th class="px-4 py-2">Trạng thái</th><th class="px-4 py-2"></th></tr>
        </thead>
        <tbody>
        @forelse ($products as $p)
            <tr class="border-t border-stone-100">
                <td class="px-4 py-2"><img src="{{ $p->thumbnail_url }}" alt="" class="h-10 w-10 rounded object-cover bg-stone-200" onerror="this.style.visibility='hidden'"></td>
                <td class="px-4 py-2 font-mono">{{ $p->sku }}</td>
                <td class="px-4 py-2 font-semibold">{{ $p->name }}</td>
                <td class="px-4 py-2">{{ $p->category->name }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($p->base_price, 0, ',', '.') }}đ/{{ $p->unit }}</td>
                <td class="px-4 py-2 text-right">{{ $p->batches_count }}</td>
                <td class="px-4 py-2">
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $p->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $p->status === 'active' ? 'Đang bán' : 'Ngừng bán' }}</span>
                </td>
                <td class="px-4 py-2 whitespace-nowrap">
                    <a href="{{ route('admin.products.edit', $p) }}" class="text-emerald-700 underline">Sửa</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $p) }}" class="inline" onsubmit="return confirm('Xóa sản phẩm {{ $p->name }}?');">
                        @csrf @method('DELETE')
                        <button class="ml-3 text-red-700 underline">Xóa</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500">Chưa có sản phẩm.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection

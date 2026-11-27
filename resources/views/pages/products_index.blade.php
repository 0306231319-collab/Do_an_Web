@extends('layouts.app')

@section('title', 'Sản phẩm - Nông Sản Sạch')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10">

    {{-- Tiêu đề --}}
    <div class="mb-6 rounded-2xl bg-green-800 px-8 py-8 text-white shadow-md">
        <p class="text-sm font-semibold uppercase tracking-wide text-green-200">Cửa hàng</p>
        <h1 class="mt-1 text-3xl font-bold">Nông sản sạch từ trang trại</h1>
        <p class="mt-2 max-w-2xl text-sm text-green-100">Chọn sản phẩm để xem lô đang bán, hạn sử dụng và truy xuất nguồn gốc bằng mã QR.</p>
    </div>

    {{-- Tìm kiếm và bộ lọc --}}
    <form method="GET" action="{{ route('products.index') }}"
          class="mb-8 grid grid-cols-1 gap-3 rounded-2xl border border-green-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-5">
        <label class="relative">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-green-700"><x-icon name="search" class="h-5 w-5" /></span>
            <input type="text" name="keyword" value="{{ request('keyword') ?? request('search') }}" placeholder="Tìm tên nông sản..."
                   class="w-full rounded-xl border border-green-300 py-2.5 pl-10 pr-3 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-200">
        </label>

        <select name="category_id"
                class="w-full rounded-xl border border-green-300 bg-white px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-200">
            <option value="">Tất cả danh mục</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ (request('category_id') ?? request('category')) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>

        <input type="number" name="min_price" value="{{ request('min_price') }}" min="0" step="1000" placeholder="Giá từ (đ)"
               class="w-full rounded-xl border border-green-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-200">

        <input type="number" name="max_price" value="{{ request('max_price') }}" min="0" step="1000" placeholder="Giá đến (đ)"
               class="w-full rounded-xl border border-green-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-200">

        <div class="flex gap-2">
            <button type="submit" class="flex-1 rounded-xl bg-green-700 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-green-800">Tìm / Lọc</button>
            @if (request()->hasAny(['keyword', 'search', 'category_id', 'category', 'min_price', 'max_price']))
                <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-xl border border-green-300 px-4 py-2.5 text-sm font-medium text-green-800 hover:bg-green-50">Xóa lọc</a>
            @endif
        </div>
    </form>

    <p class="mb-4 text-sm text-gray-600">Tìm thấy <b class="text-green-800">{{ $products->total() }}</b> sản phẩm</p>

    {{-- Lưới sản phẩm --}}
    @if ($products->isEmpty())
        <div class="rounded-2xl border border-dashed border-green-300 bg-white p-10 text-center">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-700"><x-icon name="search" class="h-7 w-7" /></div>
            <p class="font-semibold text-gray-800">Không tìm thấy sản phẩm phù hợp</p>
            <p class="mt-1 text-sm text-gray-500">Hãy thử đổi từ khóa hoặc bỏ bớt điều kiện lọc.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($products as $product)
                @php
                    $finalPrice = $product->discount_price ?? $product->base_price ?? 0;
                    $onSale = !empty($product->discount_price) && $product->discount_price < $product->base_price;
                @endphp
                <article class="flex flex-col overflow-hidden rounded-2xl border border-green-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <a href="{{ route('products.show', $product->id) }}" class="relative block">
                        @if (!empty($product->thumbnail_url))
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="h-44 w-full object-cover">
                        @else
                            <div class="flex h-44 w-full items-center justify-center bg-green-50 text-green-600"><x-icon name="leaf" class="h-14 w-14" /></div>
                        @endif
                        @if ($onSale)
                            <span class="absolute left-3 top-3 rounded-full bg-red-600 px-2.5 py-0.5 text-xs font-bold text-white">Giảm giá</span>
                        @endif
                    </a>

                    <div class="flex flex-1 flex-col p-5">
                        <span class="w-fit rounded-full border border-green-200 bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-800">{{ $product->category->name ?? 'Nông sản' }}</span>
                        <h2 class="mt-3 text-lg font-semibold text-gray-900">
                            <a href="{{ route('products.show', $product->id) }}" class="hover:text-green-700">{{ $product->name }}</a>
                        </h2>
                        <p class="mt-1 line-clamp-2 flex-1 text-sm text-gray-500">{{ $product->description }}</p>

                        <div class="mt-4 flex items-end justify-between gap-2 border-t border-green-100 pt-4">
                            <div>
                                <span class="text-lg font-bold text-green-700">{{ number_format($finalPrice, 0, ',', '.') }}đ<span class="text-xs font-medium text-gray-500">/{{ $product->unit ?? 'kg' }}</span></span>
                                @if ($onSale)
                                    <span class="block text-xs text-gray-400 line-through">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                                @endif
                            </div>
                            <a href="{{ route('products.show', $product->id) }}" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-800">Chi tiết</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $products->links() }}</div>
    @endif
</div>
@endsection

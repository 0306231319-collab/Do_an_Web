@extends('layouts.app')

@section('title', 'Danh sách sản phẩm - Nông Sản Sạch')

@section('content')
    <x-container class="py-10">
        {{-- Tiêu đề trang --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-green-900 md:text-3xl">Danh sách nông sản sạch</h1>
            <p class="mt-1 text-sm text-gray-600">Chọn sản phẩm để xem thông tin chi tiết và truy xuất nguồn gốc lô hàng.</p>
        </div>

        {{-- Thanh Tìm kiếm & Bộ lọc (Danh mục + Khoảng giá) --}}
        <form method="GET" action="{{ route('products.index') }}" class="mb-8 grid grid-cols-1 gap-3 rounded-2xl border border-[#E6E3D8] bg-white p-4 shadow-xs sm:grid-cols-2 lg:grid-cols-5">
            {{-- Ô tìm kiếm từ khóa --}}
            <div>
                <input type="text" name="keyword" value="{{ request('keyword') ?? request('search') }}"
                       placeholder="Tìm tên nông sản..."
                       class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm focus:border-[#1F5C34] focus:outline-none">
            </div>

            {{-- Bộ lọc danh mục --}}
            <div>
                <select name="category_id" class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm focus:border-[#1F5C34] focus:outline-none">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (request('category_id') ?? request('category')) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Giá tối thiểu --}}
            <div>
                <input type="number" name="min_price" value="{{ request('min_price') }}" min="0" step="1000"
                       placeholder="Giá từ (đ)"
                       class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm focus:border-[#1F5C34] focus:outline-none">
            </div>

            {{-- Giá tối đa --}}
            <div>
                <input type="number" name="max_price" value="{{ request('max_price') }}" min="0" step="1000"
                       placeholder="Giá đến (đ)"
                       class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm focus:border-[#1F5C34] focus:outline-none">
            </div>

            {{-- Nút Lọc & Đặt lại --}}
            <div class="flex gap-2">
                <button type="submit"
                        style="background-color: #1F5C34;"
                        class="flex-1 rounded-xl px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90 cursor-pointer">
                    Tìm / Lọc
                </button>
                @if(request()->hasAny(['keyword', 'search', 'category_id', 'category', 'min_price', 'max_price']))
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Xóa
                    </a>
                @endif
            </div>
        </form>

        {{-- Lưới sản phẩm --}}
        @if($products->isEmpty())
            <div class="rounded-xl bg-white p-8 text-center text-gray-500 shadow-sm">
                Không tìm thấy sản phẩm nào phù hợp với điều kiện lọc.
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach($products as $product)
                    @php
                        $finalPrice = $product->discount_price ?? $product->base_price ?? 0;
                    @endphp
                    <div class="flex flex-col justify-between overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                        <div>
                            @if(!empty($product->thumbnail_url))
                                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="h-44 w-full object-cover">
                            @endif
                            <div class="p-5 pb-0">
                                <span class="inline-block rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    {{ $product->category->name ?? 'Nông sản' }}
                                </span>
                                <h2 class="mt-3 text-lg font-semibold text-gray-800">{{ $product->name }}</h2>
                                <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $product->description }}</p>
                            </div>
                        </div>

                        <div class="m-5 mt-4 pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                            <div>
                                <span class="font-bold text-green-700">
                                    {{ number_format($finalPrice, 0, ',', '.') }} đ/{{ $product->unit ?? 'kg' }}
                                </span>
                                @if(!empty($product->discount_price) && $product->discount_price < $product->base_price)
                                    <span class="block text-xs text-gray-400 line-through">
                                        {{ number_format($product->base_price, 0, ',', '.') }} đ
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('products.show', $product->id) }}">
                                <x-button color="green">Chi tiết</x-button>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </x-container>
@endsection

@extends('layouts.app')

@section('title', 'Trang chủ - Nông Sản Sạch')

@section('content')
<x-container class="py-10">

    {{-- Banner giới thiệu --}}
    <section class="rounded-2xl bg-green-100 p-6 md:p-12">
        <h1 class="text-3xl font-bold text-green-800 md:text-5xl">
            Nông sản sạch từ nông trại
        </h1>

        <p class="mt-4 max-w-xl text-gray-700">
            Truy xuất nguồn gốc rõ ràng. Mua sắm nông sản tươi sạch và an toàn.
        </p>

        <a href="#products"
           class="mt-6 inline-flex rounded-lg bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800">
            Khám phá sản phẩm
        </a>
    </section>

    {{-- Danh sách sản phẩm --}}
    <section id="products" class="mt-10">
        <h2 class="mb-6 text-2xl font-bold">
            Sản phẩm nổi bật
        </h2>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3">

            @forelse ($products as $product)

                @php
                    $price = ($product->discount_price && $product->discount_price > 0)
                        ? $product->discount_price
                        : $product->base_price;
                @endphp

                <article
                    x-data="{
                        loading: false,

                        async addToCart(productId) {
                            if (this.loading) return;

                            this.loading = true;

                            try {
                                const response = await window.apiFetch('/api/cart', {
                                    method: 'POST',
                                    body: JSON.stringify({
                                        product_id: productId,
                                        quantity: 1
                                    })
                                });

                                const result = await response.json();

                                if (!response.ok || !result.success) {
                                    throw new Error(
                                        result.message || 'Không thể thêm sản phẩm vào giỏ hàng.'
                                    );
                                }

                                window.dispatchEvent(new CustomEvent('toast', {
                                    detail: {
                                        message: 'Đã thêm sản phẩm vào giỏ hàng.'
                                    }
                                }));

                            } catch (error) {
                                window.dispatchEvent(new CustomEvent('toast', {
                                    detail: {
                                        message: error.message || 'Có lỗi xảy ra.'
                                    }
                                }));

                            } finally {
                                this.loading = false;
                            }
                        }
                    }"
                    class="rounded-xl bg-white p-5 shadow-sm transition hover:shadow-md"
                >

                    {{-- Ảnh sản phẩm --}}
                    <img
                        src="{{ $product->thumbnail_url }}"
                        alt="{{ $product->name }}"
                        class="mb-4 h-44 w-full rounded-lg bg-gray-100 object-cover"
                        onerror="this.style.visibility='hidden'"
                    >

                    {{-- Tên sản phẩm --}}
                    <h3 class="font-semibold">
                        {{ $product->name }}
                    </h3>

                    {{-- Mô tả --}}
                    <p class="mt-2 line-clamp-2 text-sm text-gray-500">
                        {{ $product->description }}
                    </p>

                    {{-- Giá sản phẩm --}}
                    <p class="mt-3 font-bold text-green-700">
                        {{ number_format($price, 0, ',', '.') }}đ/{{ $product->unit }}
                    </p>

                    {{-- Nút thêm giỏ hàng AJAX --}}
                    <button
                        type="button"
                        @click="addToCart({{ $product->id }})"
                        :disabled="loading"
                        class="mt-4 w-full rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span x-text="loading ? 'Đang thêm...' : 'Thêm vào giỏ hàng'"></span>
                    </button>

                </article>

            @empty

                <p class="text-gray-500">
                    Hiện chưa có sản phẩm đang kinh doanh.
                </p>

            @endforelse

        </div>
    </section>

</x-container>
@endsection

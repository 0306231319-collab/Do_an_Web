@extends('layouts.app')

@section('title', 'Trang chủ - Nông Sản Sạch')

@section('content')
<x-container class="py-8">

    {{-- ===== Banner ===== --}}
    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-green-700 to-green-500 p-8 text-white shadow-lg md:p-14">
        <p class="mb-2 inline-block rounded-full bg-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
            Single Farm · Truy xuất bằng mã QR
        </p>
        <h1 class="text-3xl font-extrabold leading-tight md:text-5xl">
            Nông sản sạch<br>từ nông trại đến bàn ăn
        </h1>
        <p class="mt-4 max-w-xl text-green-50">
            Biết rõ ngày thu hoạch, hạn sử dụng và nhật ký chăm sóc của từng lô hàng.
            Hàng hết hạn trước được bán trước, luôn tươi mỗi ngày.
        </p>
        <div class="mt-7 flex flex-wrap gap-3">
            <a href="{{ Route::has('products.index') ? route('products.index') : '#products' }}"
               class="rounded-xl bg-white px-6 py-3 font-semibold text-green-800 shadow hover:bg-green-50">
                Khám phá sản phẩm
            </a>
            @if (Route::has('subscriptions.index'))
                <a href="{{ route('subscriptions.index') }}"
                   class="rounded-xl border border-white/70 px-6 py-3 font-semibold text-white hover:bg-white/10">
                    Đăng ký gói định kỳ
                </a>
            @endif
        </div>
    </section>

    {{-- ===== 3 điểm nổi bật ===== --}}
    <section class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
               @foreach ([
            ['search', 'Truy xuất nguồn gốc', 'Quét mã QR xem ngày thu hoạch, người phụ trách và nhật ký canh tác.'],
            ['leaf', 'Tươi theo lô', 'Hệ thống tự ưu tiên lô sắp hết hạn, bạn luôn nhận hàng tươi.'],
            ['star', 'Tích điểm thành viên', 'Mỗi 10.000đ mua hàng được 1 điểm khi đơn được giao.'],
        ] as [$icon, $title, $desc])
            <div class="flex items-start gap-4 rounded-2xl border border-green-200 bg-white p-5 shadow-sm">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-700">
                    <x-icon :name="$icon" class="h-7 w-7" />
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ===== Sản phẩm nổi bật ===== --}}
    <section id="products" class="mt-12">
        <div class="mb-6 flex items-end justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Sản phẩm nổi bật</h2>
            @if (Route::has('products.index'))
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-green-700 hover:underline">
                    Xem tất cả →
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($products as $product)
                @php
                    $price = ($product->discount_price && $product->discount_price > 0)
                        ? $product->discount_price
                        : $product->base_price;
                    $hasSale = $product->discount_price && $product->discount_price > 0
                        && $product->discount_price < $product->base_price;
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
                                    body: JSON.stringify({ product_id: productId, quantity: 1 })
                                });
                                const result = await response.json();
                                if (!response.ok || !result.success) {
                                    throw new Error(result.message || 'Không thể thêm sản phẩm vào giỏ hàng.');
                                }
                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Đã thêm sản phẩm vào giỏ hàng.' } }));
                            } catch (error) {
                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: error.message || 'Có lỗi xảy ra.' } }));
                            } finally {
                                this.loading = false;
                            }
                        }
                    }"
                    class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    {{-- Ảnh: có khung dự phòng khi thiếu ảnh --}}
                    <div class="relative flex h-48 items-center justify-center bg-gradient-to-br from-green-50 to-green-100 text-6xl">
                        <span aria-hidden="true">🥬</span>
                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}"
                             class="absolute inset-0 h-full w-full object-cover"
                             loading="lazy" onerror="this.remove()">
                        @if ($hasSale)
                            <span class="absolute left-3 top-3 rounded-full bg-red-500 px-2.5 py-1 text-xs font-bold text-white">Khuyến mãi</span>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                        <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $product->description }}</p>

                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-xl font-bold text-green-700">
                                {{ number_format($price, 0, ',', '.') }}đ<span class="text-sm font-medium text-gray-500">/{{ $product->unit }}</span>
                            </span>
                            @if ($hasSale)
                                <span class="text-sm text-gray-400 line-through">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                            @endif
                        </div>

                        <div class="mt-auto flex gap-2 pt-4">
                            @if (Route::has('products.show'))
                                <a href="{{ route('products.show', $product->id) }}"
                                   class="flex-1 rounded-xl border border-green-600 px-3 py-2 text-center text-sm font-semibold text-green-700 hover:bg-green-50">
                                    Xem chi tiết
                                </a>
                            @endif
                            <button type="button" @click="addToCart({{ $product->id }})" :disabled="loading"
                                    class="flex-1 rounded-xl bg-green-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <span x-text="loading ? 'Đang thêm...' : 'Thêm vào giỏ'"></span>
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <p class="text-gray-500">Hiện chưa có sản phẩm đang kinh doanh.</p>
            @endforelse
        </div>
    </section>
</x-container>
@endsection

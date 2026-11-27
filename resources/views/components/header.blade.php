@php
    $sampleBatchCode = \App\Models\Batch::latest()->value('batch_code');
@endphp
<header class="sticky top-0 z-30 border-b border-[#E5E2D9] bg-[#1E5631] text-white shadow-xs">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        {{-- Logo / Tên thương hiệu --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2 text-sm font-bold tracking-tight sm:text-base">
            <span>Nông Sản Sạch</span>
        </a>

        {{-- Menu điều hướng --}}
        <nav class="flex items-center gap-3 text-xs font-medium sm:gap-5 sm:text-sm">
            <a href="{{ url('/') }}" class="hover:text-emerald-200 transition">Trang chủ</a>
            <a href="{{ \Illuminate\Support\Facades\Route::has('products.index') ? route('products.index') : '#' }}"
               class="hover:text-emerald-200 transition">Sản phẩm</a>
            <a href="{{ \Illuminate\Support\Facades\Route::has('farm.about') ? route('farm.about') : '#' }}"
               class="hover:text-emerald-200 transition">Nông trại</a>
            <a href="{{ $sampleBatchCode && \Illuminate\Support\Facades\Route::has('traceability.show') ? route('traceability.show', ['batch_code' => $sampleBatchCode]) : '#' }}"
               class="hover:text-emerald-200 transition">Truy xuất QR</a>
            <a href="{{ \Illuminate\Support\Facades\Route::has('farm.batches.index') ? route('farm.batches.index') : '#' }}"
               class="rounded-lg bg-white/15 px-2.5 py-1 hover:bg-white/25 transition">Lô hàng</a>
        </nav>
    </div>
</header>
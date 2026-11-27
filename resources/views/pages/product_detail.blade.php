@extends('layouts.app')

@section('title', $product->name)
@section('show_header', true)
@section('show_footer', true)

@section('content')
<div class="mx-auto max-w-5xl px-4 py-6 space-y-6" x-data="{ buyMode: 'retail', qty: 1 }">
    {{-- Phần thông tin chính sản phẩm --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 rounded-2xl border border-[#E6E3D8] bg-white p-5 md:p-6 shadow-xs">
        <div class="aspect-4/3 w-full overflow-hidden rounded-xl bg-emerald-50">
            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}"
                 onerror="this.src='https://placehold.co/600x450/E8F5E9/1E5631?text=Nong+San+Sach'"
                 class="h-full w-full object-cover">
        </div>

        <div class="flex flex-col justify-between space-y-4">
            <div>
                <span class="inline-block rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-[#1E5631]">
                    {{ $product->category->name ?? 'Nông sản sạch' }}
                </span>
                <h1 class="mt-2 text-2xl font-bold text-[#111827]">{{ $product->name }}</h1>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-[#1E5631]">
                        {{ number_format($product->discount_price ?? $product->base_price, 0, ',', '.') }}đ
                    </span>
                    <span class="text-sm text-[#6B7280]">/ {{ $product->unit }}</span>
                </div>
                <p class="mt-3 text-sm text-[#374151] leading-relaxed">{{ $product->description }}</p>

                {{-- Thông tin lô đang bán theo FEFO --}}
                @if($currentBatch)
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-[#F7F6F0] p-3.5 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#1E5631]">Lô đang mở bán: {{ $currentBatch->batch_code }}</span>
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 font-semibold text-[#1E5631]">
                                Còn {{ $currentBatch->current_quantity }} {{ $product->unit }}
                            </span>
                        </div>
                        <div class="text-[#374151]">
                            Ngày thu hoạch: <strong>{{ optional($currentBatch->harvest_date)->format('d/m/Y') }}</strong> ·
                            Hạn sử dụng: <strong class="text-[#B91C1C]">{{ optional($currentBatch->expiry_date)->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                @else
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-semibold text-[#B91C1C]">
                        Sản phẩm tạm hết lô hàng khả dụng.
                    </div>
                @endif
            </div>

            {{-- Chọn hình thức Mua lẻ hoặc Gói định kỳ --}}
            <div class="space-y-3 pt-2 border-t border-gray-100">
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="buyMode = 'retail'"
                            :class="buyMode === 'retail' ? 'border-[#1E5631] bg-emerald-50 text-[#1E5631]' : 'border-gray-200 bg-white text-gray-700'"
                            class="rounded-xl border-2 py-2.5 text-xs font-bold transition cursor-pointer">
                        Mua lẻ giao ngay
                    </button>
                    @if($product->is_subscription)
                        <button type="button" @click="buyMode = 'subscription'"
                                :class="buyMode === 'subscription' ? 'border-[#1E5631] bg-emerald-50 text-[#1E5631]' : 'border-gray-200 bg-white text-gray-700'"
                                class="rounded-xl border-2 py-2.5 text-xs font-bold transition cursor-pointer">
                            Đặt gói định kỳ (1 tháng)
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <input type="number" x-model.number="qty" min="1" class="w-20 rounded-xl border border-gray-300 px-3 py-2.5 text-center text-sm font-bold">
                    <button type="button" {{ !$currentBatch ? 'disabled' : '' }}
                            class="flex-1 rounded-xl bg-[#1E5631] py-3 text-sm font-bold text-white hover:bg-[#164225] disabled:opacity-50 transition cursor-pointer">
                        <span x-text="buyMode === 'retail' ? 'Thêm vào giỏ hàng' : 'Đăng ký gói định kỳ'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Mã QR & Timeline Nhật ký canh tác của lô đang bán --}}
    @if($currentBatch)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Cột trái: Mã QR lô hàng --}}
            <div class="rounded-2xl border border-[#E6E3D8] bg-white p-5 text-center flex flex-col items-center justify-center shadow-xs">
                <h2 class="text-sm font-bold text-[#111827]">Mã QR Truy xuất nguồn gốc</h2>
                <p class="mt-1 text-xs text-[#6B7280]">Quét bằng camera điện thoại để xem nhật ký</p>
                <div class="my-4 rounded-xl border border-gray-200 p-3 bg-white">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate(route('traceability.show', $currentBatch->batch_code)) !!}
                </div>
                <a href="{{ route('traceability.show', $currentBatch->batch_code) }}"
                   class="text-xs font-semibold text-[#1E5631] underline">
                    Xem trang truy xuất chi tiết →
                </a>
            </div>

            {{-- Cột phải: Timeline Nhật ký canh tác --}}
            <div class="md:col-span-2 rounded-2xl border border-[#E6E3D8] bg-white p-5 shadow-xs">
                <h2 class="text-base font-bold text-[#111827]">Nhật ký canh tác lô {{ $currentBatch->batch_code }}</h2>
                @if($currentBatch->farmingLogs->isEmpty())
                    <p class="mt-3 text-sm text-[#6B7280]">Đang cập nhật nhật ký canh tác cho lô này.</p>
                @else
                    <ol class="relative mt-4 border-s-2 border-emerald-200 ml-3 space-y-4">
                        @foreach($currentBatch->farmingLogs as $log)
                            <li class="ms-5">
                                <span class="absolute -start-2 h-4 w-4 rounded-full bg-[#1E5631] ring-4 ring-white"></span>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-bold text-[#111827]">{{ $log->hanh_dong }}</span>
                                    <span class="text-xs text-[#6B7280]">{{ optional($log->thoi_gian)->format('d/m/Y') }}</span>
                                </div>
                                <p class="mt-0.5 text-xs text-[#374151]">{{ $log->mo_ta_chi_tiet }}</p>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
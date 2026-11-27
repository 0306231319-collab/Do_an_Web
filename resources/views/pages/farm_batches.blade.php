@extends('layouts.app')

@section('title', 'Lô hàng và hao hụt')

@section('content')

@php
    $targetBatchCode = $latestBatchCode ?? \App\Models\Batch::latest()->value('batch_code');
@endphp
{{-- Khung chính: Mobile chiếm 100% chiều rộng (min-h-screen), PC tự mở rộng max-w-5xl bo góc đẹp mắt --}}
<div class="mx-auto flex min-h-screen md:min-h-[calc(100vh-2rem)] md:my-4 w-full max-w-5xl flex-col justify-between bg-[#F7F6F0] md:rounded-2xl md:border md:border-[#E6E3D8] md:shadow-md overflow-hidden"
     x-data="{
        tab: 'all',
        batches: [
            {
                id: 1,
                name: 'Cà chua bi',
                code: 'LO-0312',
                daysLeft: 2,
                badgeText: 'Còn 2 ngày',
                badgeStyle: 'background-color: #FCE8E6; color: #C5221F;',
                harvestDate: '10/11',
                expiryDate: '22/11',
                stock: 18,
                loss: 5,
                solidBtn: true
            },
            {
                id: 2,
                name: 'Rau muống',
                code: 'LO-0315',
                daysLeft: 4,
                badgeText: 'Còn 4 ngày',
                badgeStyle: 'background-color: #FEF3C7; color: #B45309;',
                harvestDate: '18/11',
                expiryDate: '24/11',
                stock: 30,
                loss: 0,
                solidBtn: false
            },
            {
                id: 3,
                name: 'Xà lách',
                code: 'LO-0318',
                daysLeft: 6,
                badgeText: 'Còn 6 ngày',
                badgeStyle: 'background-color: #DCFCE7; color: #15803D;',
                harvestDate: '20/11',
                expiryDate: '26/11',
                stock: 12,
                loss: 1,
                solidBtn: false
            }
        ],
        get filteredBatches() {
            if (this.tab === 'expiring') return this.batches.filter(b => b.daysLeft <= 2);
            if (this.tab === 'valid') return this.batches.filter(b => b.daysLeft > 2);
            return this.batches;
        }
     }">

    <div>
        {{-- Header xanh đậm đúng màu Figma #1F5C34 --}}
        <header style="background-color: #1F5C34;" class="px-4 py-5 md:px-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h1 class="text-[20px] md:text-[24px] font-bold leading-tight tracking-[-0.01em]">Lô hàng và hao hụt</h1>
                    <p class="mt-1 text-[11px] md:text-[13px] font-normal text-[#D8EADF]">
                        Nhân viên nông trại · 20/11 · dữ liệu minh họa
                    </p>
                </div>
                <a href="{{ url('/') }}" class="hidden sm:inline-flex items-center rounded-lg bg-white/15 px-3 py-1.5 text-xs font-medium hover:bg-white/25 transition">
                    ← Về trang chủ
                </a>
            </div>
        </header>

        <div class="space-y-4 px-3.5 pt-4 pb-6 md:px-6">
            {{-- 3 thẻ thống kê: tự giãn đều trên mọi kích thước màn hình --}}
            <div class="grid grid-cols-3 gap-2.5 md:gap-4">
                <div class="rounded-xl border border-[#E6E3D8] bg-white py-3 px-2 md:py-4 text-center shadow-2xs">
                    <div class="text-[16px] md:text-[22px] font-bold leading-none text-[#1F2937]">3</div>
                    <div class="mt-1.5 text-[10px] md:text-[12px] text-[#6B7280]">Lô đang bán</div>
                </div>
                <div class="rounded-xl border border-[#E6E3D8] bg-white py-3 px-2 md:py-4 text-center shadow-2xs">
                    <div class="text-[16px] md:text-[22px] font-bold leading-none text-[#DC2626]">1</div>
                    <div class="mt-1.5 text-[10px] md:text-[12px] text-[#6B7280]">Sắp hết hạn</div>
                </div>
                <div class="rounded-xl border border-[#E6E3D8] bg-white py-3 px-2 md:py-4 text-center shadow-2xs">
                    <div class="text-[16px] md:text-[22px] font-bold leading-none text-[#1F2937]">6 kg</div>
                    <div class="mt-1.5 text-[10px] md:text-[12px] text-[#6B7280]">Hao hụt tuần</div>
                </div>
            </div>

            {{-- Tab lọc --}}
            <div class="flex flex-wrap items-center gap-2 pt-0.5">
                <button type="button" @click="tab = 'all'"
                        :style="tab === 'all' ? 'background-color: #1F5C34; color: #FFFFFF; border-color: #1F5C34;' : 'background-color: #FFFFFF; color: #374151; border-color: #D1D5DB;'"
                        class="rounded-full border px-4 py-1.5 text-[12px] md:text-[13px] font-semibold leading-none transition cursor-pointer">
                    Tất cả
                </button>
                <button type="button" @click="tab = 'expiring'"
                        :style="tab === 'expiring' ? 'background-color: #1F5C34; color: #FFFFFF; border-color: #1F5C34;' : 'background-color: #FFFFFF; color: #374151; border-color: #D1D5DB;'"
                        class="rounded-full border px-4 py-1.5 text-[12px] md:text-[13px] font-semibold leading-none transition cursor-pointer">
                    Sắp hết hạn
                </button>
                <button type="button" @click="tab = 'valid'"
                        :style="tab === 'valid' ? 'background-color: #1F5C34; color: #FFFFFF; border-color: #1F5C34;' : 'background-color: #FFFFFF; color: #374151; border-color: #D1D5DB;'"
                        class="rounded-full border px-4 py-1.5 text-[12px] md:text-[13px] font-semibold leading-none transition cursor-pointer">
                    Còn hạn
                </button>
            </div>

            {{-- Danh sách lô hàng: 1 cột trên Mobile, 2 cột trên Tablet, 3 cột trên Desktop --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                <template x-for="batch in filteredBatches" :key="batch.id">
                    <div class="flex flex-col justify-between rounded-2xl border border-[#E6E3D8] bg-white p-3.5 md:p-4 shadow-2xs">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="text-[14px] md:text-[15px] font-bold leading-snug text-[#111827]"
                                     x-text="`${batch.name} · ${batch.code}`"></div>
                                <div class="mt-0.5 text-[11px] md:text-[12px] text-[#6B7280]"
                                     x-text="`Thu hoạch ${batch.harvestDate} · Hạn sử dụng ${batch.expiryDate}`"></div>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] md:text-[11px] font-semibold leading-none"
                                  :style="batch.badgeStyle"
                                  x-text="batch.badgeText"></span>
                        </div>

                        <div class="mt-4 flex items-end justify-between gap-2 pt-2 border-t border-gray-100">
                            <div class="space-y-0.5 text-[12px] md:text-[13px] leading-snug text-[#374151]">
                                <div>Tồn <strong class="font-bold text-[#111827]" x-text="`${batch.stock} kg`"></strong></div>
                                <div>Hao hụt <strong class="font-bold text-[#111827]" x-text="`${batch.loss} kg`"></strong></div>
                            </div>

                            <a :href="`/farm/batches/${batch.id}/dispose`"
                               :style="batch.solidBtn
                                    ? 'background-color: #B91C1C; color: #FFFFFF; border: 1px solid #B91C1C;'
                                    : 'background-color: #FFFFFF; color: #B91C1C; border: 1px solid #B91C1C;'"
                               class="rounded-lg px-4 py-2 text-[12px] font-semibold leading-none transition hover:opacity-90">
                                Xuất hủy
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Bottom Navigation 4 mục --}}
    <nav class="sticky bottom-0 grid grid-cols-4 border-t border-[#E5E7EB] bg-white pt-2 pb-2.5 md:py-3 text-center">
        <a href="{{ url('/farm/batches') }}" class="flex flex-col md:flex-row md:justify-center items-center gap-1 md:gap-2 text-[10px] md:text-[13px] font-semibold text-[#1F5C34]">
            <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span>Lô hàng</span>
        </a>
        <a href="{{ $targetBatchCode && \Illuminate\Support\Facades\Route::has('traceability.show') ? route('traceability.show', ['batch_code' => $targetBatchCode]) : '#' }}"
        class="flex flex-col md:flex-row md:justify-center items-center gap-1 md:gap-2 text-[10px] md:text-[13px] font-medium text-[#6B7280] hover:text-[#1F5C34]">
            <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
            <span>Quét QR</span>
        </a>
                <a href="{{ route('products.index') }}" class="flex flex-col md:flex-row md:justify-center items-center gap-1 md:gap-2 text-[10px] md:text-[13px] font-medium text-[#6B7280] hover:text-[#1F5C34]">
            <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Sản phẩm</span>
        </a>
        <a href="{{ route('login') }}" class="flex flex-col md:flex-row md:justify-center items-center gap-1 md:gap-2 text-[10px] md:text-[13px] font-medium text-[#6B7280] hover:text-[#1F5C34]">
            <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>{{ auth()->check() ? auth()->user()->name : 'Tài khoản' }}</span>
        </a>
    </nav>
</div>
@endsection
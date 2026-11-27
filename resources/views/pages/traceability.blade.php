@extends('layouts.app')

@section('title', 'Truy xuất nguồn gốc - ' . $batch->batch_code)
@section('show_header', true)
@section('show_footer', true)

@section('content')
<div class="mx-auto max-w-4xl px-4 py-6 space-y-6">
    {{-- Thẻ thông tin tổng quan lô hàng --}}
    <div class="rounded-2xl border border-[#E6E3D8] bg-white p-5 md:p-6 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-4">
            <div>
                <span class="inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-[#1E5631]">
                    Chứng nhận VietGAP · Hữu cơ Single Farm
                </span>
                <h1 class="mt-2 text-xl md:text-2xl font-bold text-[#111827]">
                    {{ $batch->product->name ?? 'Nông sản sạch' }}
                </h1>
                <p class="mt-1 text-sm text-[#4B5563]">
                    Mã lô: <strong class="text-[#1E5631]">{{ $batch->batch_code }}</strong>
                </p>
            </div>

            {{-- Hiển thị mã QR của lô --}}
            <div class="flex flex-col items-center rounded-xl border border-[#E6E3D8] bg-[#F7F6F0] p-3">
                @if(str_starts_with($batch->qr_code_url ?? '', '/storage/'))
                    <img src="{{ asset($batch->qr_code_url) }}" alt="QR {{ $batch->batch_code }}" class="h-24 w-24 object-contain">
                @else
                    {{-- Render QR trực tiếp SVG nếu chưa bấm lưu file PNG --}}
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(96)->generate(route('traceability.show', $batch->batch_code)) !!}
                @endif
                <span class="mt-1.5 text-[11px] font-medium text-[#6B7280]">Quét để truy xuất</span>
            </div>
        </div>

        {{-- Thông tin người trồng, ngày thu hoạch, hạn dùng --}}
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl bg-[#F7F6F0] p-3">
                <div class="text-xs text-[#6B7280]">Người phụ trách canh tác</div>
                <div class="mt-1 font-bold text-[#111827]">{{ $batch->creator->full_name ?? 'Nhân viên nông trại' }}</div>
            </div>
            <div class="rounded-xl bg-[#F7F6F0] p-3">
                <div class="text-xs text-[#6B7280]">Ngày thu hoạch</div>
                <div class="mt-1 font-bold text-[#111827]">{{ optional($batch->harvest_date)->format('d/m/Y') }}</div>
            </div>
            <div class="rounded-xl bg-[#F7F6F0] p-3">
                <div class="text-xs text-[#6B7280]">Hạn sử dụng tốt nhất</div>
                <div class="mt-1 font-bold text-[#B91C1C]">{{ optional($batch->expiry_date)->format('d/m/Y') }}</div>
            </div>
        </div>

        {{-- Khu vực thao tác dành riêng cho Farm Staff (Tạo QR) --}}
        @auth
            @if(auth()->user()->role === 'farm_staff')
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center gap-3">
                    <form method="POST" action="{{ route('farm.batches.qr', $batch->id) }}">
                        @csrf
                        <button type="submit" class="rounded-xl bg-[#1E5631] px-4 py-2 text-xs font-semibold text-white hover:bg-[#164225] transition cursor-pointer">
                            Tạo / Xuất file ảnh QR PNG
                        </button>
                    </form>
                </div>
            @endif
        @endauth
    </div>

    {{-- Form nhập nhật ký canh tác (chỉ hiện khi đăng nhập vai trò farm_staff) --}}
    @auth
        @if(auth()->user()->role === 'farm_staff')
            <div class="rounded-2xl border border-[#E6E3D8] bg-white p-5 md:p-6 shadow-xs">
                <h2 class="text-base md:text-lg font-bold text-[#111827]">Cập nhật nhật ký canh tác mới</h2>
                <form method="POST" action="{{ route('farm.batches.logs.store', $batch->id) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#111827]">Loại công việc *</label>
                            <input type="text" name="hanh_dong" required placeholder="VD: Bón phân hữu cơ, Tưới nhỏ giọt..."
                                   value="{{ old('hanh_dong') }}"
                                   class="mt-1 w-full rounded-xl border border-gray-300 px-3.5 py-2 text-sm focus:border-[#1E5631] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#111827]">Thời gian thực hiện (không ở tương lai) *</label>
                            <input type="datetime-local" name="thoi_gian" required max="{{ now()->format('Y-m-d\TH:i') }}"
                                   value="{{ old('thoi_gian', now()->format('Y-m-d\TH:i')) }}"
                                   class="mt-1 w-full rounded-xl border border-gray-300 px-3.5 py-2 text-sm focus:border-[#1E5631] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#111827]">Mô tả chi tiết</label>
                        <textarea name="mo_ta_chi_tiet" rows="2" placeholder="Ghi chú tình trạng cây trồng, liều lượng..."
                                  class="mt-1 w-full rounded-xl border border-gray-300 px-3.5 py-2 text-sm focus:border-[#1E5631] focus:outline-none">{{ old('mo_ta_chi_tiet') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#111827]">Ảnh / Video minh chứng (JPG, PNG ≤ 5MB hoặc MP4 ≤ 20MB)</label>
                        <input type="file" name="media_file" accept=".jpg,.jpeg,.png,.mp4"
                               class="mt-1 block w-full text-xs text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3.5 file:py-2 file:text-xs file:font-semibold file:text-[#1E5631]">
                    </div>

                    <button type="submit" class="rounded-xl bg-[#1E5631] px-5 py-2.5 text-xs font-bold text-white hover:bg-[#164225] transition cursor-pointer">
                        Lưu nhật ký canh tác
                    </button>
                </form>
            </div>
        @endif
    @endauth

    {{-- Timeline Nhật ký canh tác --}}
    <div class="rounded-2xl border border-[#E6E3D8] bg-white p-5 md:p-6 shadow-xs">
        <h2 class="text-base md:text-lg font-bold text-[#111827]">Dòng thời gian (Timeline) canh tác</h2>

        @if($batch->farmingLogs->isEmpty())
            <p class="mt-3 text-sm text-[#6B7280]">Chưa có nhật ký canh tác cho lô hàng này.</p>
        @else
            <ol class="relative mt-5 border-s-2 border-emerald-200 ml-3 space-y-6">
                @foreach($batch->farmingLogs as $log)
                    <li class="ms-6">
                        <span class="absolute -start-2.5 flex h-5 w-5 items-center justify-center rounded-full bg-[#1E5631] ring-4 ring-white">
                            <span class="h-2 w-2 rounded-full bg-white"></span>
                        </span>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h3 class="text-sm md:text-base font-bold text-[#111827]">{{ $log->hanh_dong }}</h3>
                            <time class="text-xs font-medium text-[#6B7280]">
                                {{ optional($log->thoi_gian)->format('d/m/Y H:i') }}
                            </time>
                        </div>
                        @if($log->mo_ta_chi_tiet)
                            <p class="mt-1 text-sm text-[#374151]">{{ $log->mo_ta_chi_tiet }}</p>
                        @endif
                        <p class="mt-1 text-xs text-[#6B7280]">
                            Thực hiện bởi: {{ $log->staff->full_name ?? 'Nhân viên nông trại' }}
                        </p>

                        @if($log->hinh_anh_minh_chung)
                            <img src="{{ $log->hinh_anh_minh_chung }}" alt="Minh chứng" class="mt-2.5 max-h-56 rounded-xl border border-gray-200 object-cover">
                        @endif

                        @if($log->video_minh_chung)
                            <video controls class="mt-2.5 max-h-64 rounded-xl border border-gray-200">
                                <source src="{{ $log->video_minh_chung }}" type="video/mp4">
                            </video>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</div>
@endsection
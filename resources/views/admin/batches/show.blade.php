
@extends('admin.layout')

@section('title', 'Lô ' . $batch->batch_code)

@section('content')

<a href="{{ route('admin.batches.index') }}"
   class="text-sm text-emerald-700 underline">
    &larr; Danh sách lô
</a>

<h1 class="text-2xl font-bold mt-2 mb-4">
    Lô {{ $batch->batch_code }}
    @include('admin.partials.expiry-badge')
</h1>

{{-- THÔNG TIN LÔ HÀNG --}}
<dl class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8 text-sm">
    @php
        $info = [
            ['Sản phẩm', $batch->product->name],
            ['Người tạo lô', $batch->creator->full_name ?? '—'],
            ['Ngày thu hoạch', $batch->harvest_date->format('d/m/Y')],
            ['Hạn sử dụng', $batch->expiry_date->format('d/m/Y')],
            [
                'Số lượng nhập',
                rtrim(rtrim(number_format($batch->initial_quantity, 2, ',', '.'), '0'), ',')
                . ' ' . $batch->product->unit
            ],
            [
                'Tồn kho hiện tại',
                rtrim(rtrim(number_format($batch->current_quantity, 2, ',', '.'), '0'), ',')
                . ' ' . $batch->product->unit
            ],
            ['Mã QR truy xuất', $batch->qr_code_url],
            ['Trạng thái lưu', $batch->status],
        ];
    @endphp

    @foreach ($info as [$k, $v])
        <div class="rounded-xl border border-stone-200 bg-white p-3">
            <dt class="text-slate-500">{{ $k }}</dt>
            <dd class="font-semibold break-all">{{ $v }}</dd>
        </div>
    @endforeach
</dl>

{{-- LỊCH SỬ XUẤT HỦY RIÊNG --}}
@php
    $disposalLogs = $logs->where('action_type', 'DISPOSAL');
@endphp

<h2 class="text-lg font-semibold mb-2 text-red-700">
    Lịch sử xuất hủy
</h2>

<div class="overflow-x-auto rounded-xl border border-red-200 bg-white mb-8">
    <table class="min-w-full text-sm">
        <thead class="bg-red-50 text-left text-slate-600">
            <tr>
                <th class="px-4 py-3">Thời điểm</th>
                <th class="px-4 py-3 text-right">Số lượng hủy</th>
                <th class="px-4 py-3 text-right">Tồn sau hủy</th>
                <th class="px-4 py-3">Người thực hiện</th>
                <th class="px-4 py-3">Lý do</th>
                <th class="px-4 py-3">Ảnh minh chứng</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($disposalLogs as $log)
                <tr class="border-t border-stone-100">
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $log->created_at?->format('d/m/Y H:i') }}
                    </td>

                    <td class="px-4 py-3 text-right font-semibold text-red-700">
                        {{ number_format(abs($log->quantity_change), 2, ',', '.') }}
                    </td>

                    <td class="px-4 py-3 text-right">
                        {{ number_format($log->balance_after, 2, ',', '.') }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $log->user->full_name ?? '—' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $log->reason ?? '—' }}
                    </td>

                    <td class="px-4 py-3">
                        @if ($log->evidence_image)
                            <a href="{{ asset('storage/' . $log->evidence_image) }}"
                               target="_blank"
                               rel="noopener noreferrer">
                                <img
                                    src="{{ asset('storage/' . $log->evidence_image) }}"
                                    alt="Ảnh minh chứng xuất hủy"
                                    class="w-16 h-16 object-cover rounded-lg border"
                                >
                            </a>
                        @else
                            <span class="text-slate-400">Không có ảnh</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                        Lô hàng này chưa có lịch sử xuất hủy.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- NHẬT KÝ CANH TÁC --}}
<h2 class="text-lg font-semibold mb-2">
    Nhật ký canh tác của lô
</h2>

<ol class="space-y-2">
    @forelse ($farmingLogs as $f)
        <li class="rounded-xl border border-stone-200 bg-white p-3 text-sm">
            <span class="font-semibold">{{ $f->hanh_dong }}</span>

            <span class="text-slate-500">
                · {{ $f->thoi_gian->format('d/m/Y H:i') }}
            </span>

            @if ($f->mo_ta_chi_tiet)
                <div class="text-slate-600">
                    {{ $f->mo_ta_chi_tiet }}
                </div>
            @endif
        </li>
    @empty
        <li class="text-slate-500 text-sm">
            Chưa có nhật ký.
        </li>
    @endforelse
</ol>

@endsection

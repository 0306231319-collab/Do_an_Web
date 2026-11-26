
@extends('admin.layout')

@section('title', 'Lô hàng và tồn kho')

@section('content')

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold mb-1">Lô hàng và tồn kho</h1>

        <p class="text-sm text-slate-500">
            Xếp theo hạn sử dụng gần nhất (thứ tự FEFO).
            Bấm mã lô để xem lịch sử xuất kho.
        </p>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.batches.create') }}"
           class="min-h-11 inline-flex items-center justify-center rounded-lg bg-emerald-700 px-5 font-semibold text-white hover:bg-emerald-800">
            + Thêm lô hàng
        </a>

        <a href="{{ route('admin.disposals.create') }}"
           class="min-h-11 inline-flex items-center justify-center rounded-lg bg-orange-600 px-5 font-semibold text-white hover:bg-orange-700">
            Xuất hủy
        </a>
    </div>
</div>

{{-- Thông báo --}}
@if (session('success'))
    <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        <p class="font-semibold mb-1">Có lỗi xảy ra:</p>

        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Cấu hình cảnh báo --}}
<div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="font-semibold text-amber-900">
                Cấu hình cảnh báo cận hạn
            </h2>

            <p class="mt-1 text-sm text-amber-800">
                Các lô còn tồn và có hạn sử dụng trong vòng
                <strong>{{ $alertDays }} ngày</strong>
                sẽ được đánh dấu cảnh báo.
            </p>
        </div>

        <form method="POST"
              action="{{ route('admin.batches.alert-days.update') }}"
              class="flex flex-wrap items-end gap-2">

            @csrf

            <div>
                <label for="near_expiry_alert_days"
                       class="mb-1 block text-sm font-medium text-amber-900">
                    Số ngày cảnh báo
                </label>

                <input
                    id="near_expiry_alert_days"
                    type="number"
                    name="near_expiry_alert_days"
                    min="1"
                    max="365"
                    required
                    value="{{ old('near_expiry_alert_days', $alertDays) }}"
                    class="min-h-11 w-28 rounded-lg border border-amber-300 bg-white px-3"
                >
            </div>

            <button type="submit"
                    class="min-h-11 rounded-lg bg-amber-700 px-4 font-semibold text-white hover:bg-amber-800">
                Lưu ngưỡng
            </button>
        </form>
    </div>
</div>

{{-- Tìm kiếm và lọc --}}
<form method="GET" class="mt-5 mb-4 flex flex-col gap-2 sm:flex-row">

    <input
        type="text"
        name="q"
        value="{{ $keyword }}"
        placeholder="Tìm theo mã lô hoặc tên sản phẩm"
        class="min-h-11 flex-1 rounded-lg border border-stone-300 px-3"
    >

    <select name="filter"
            class="min-h-11 rounded-lg border border-stone-300 px-3">

        @foreach ([
            '' => 'Tất cả',
            'selling' => 'Đang bán',
            'near' => 'Sắp hết hạn',
            'expired' => 'Quá hạn còn tồn',
            'empty' => 'Hết hàng'
        ] as $v => $l)

            <option value="{{ $v }}"
                @selected($filter == (string) $v)>
                {{ $l }}
            </option>

        @endforeach
    </select>

    <button type="submit"
            class="min-h-11 rounded-lg bg-emerald-700 px-5 font-semibold text-white hover:bg-emerald-800">
        Lọc
    </button>

    <a href="{{ route('admin.batches.index') }}"
       class="inline-flex min-h-11 items-center justify-center rounded-lg border border-stone-300 px-4 hover:bg-stone-50">
        Xóa lọc
    </a>

</form>

{{-- Bảng danh sách lô --}}
<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">

    <table class="min-w-full text-sm">

        <thead class="bg-stone-100 text-left text-slate-600">
            <tr>
                <th class="px-4 py-3">Mã lô</th>
                <th class="px-4 py-3">Sản phẩm</th>
                <th class="px-4 py-3">Thu hoạch</th>
                <th class="px-4 py-3">Hạn sử dụng</th>
                <th class="px-4 py-3 text-right">Nhập</th>
                <th class="px-4 py-3 text-right">Tồn</th>
                <th class="px-4 py-3">Tình trạng</th>
                <th class="px-4 py-3 text-center">Thao tác</th>
            </tr>
        </thead>

        <tbody>

        @forelse ($batches as $batch)

            <tr class="border-t border-stone-100 hover:bg-stone-50">

                <td class="px-4 py-3 font-mono">
                    <a class="text-emerald-700 underline"
                       href="{{ route('admin.batches.show', $batch) }}">
                        {{ $batch->batch_code }}
                    </a>
                </td>

                <td class="px-4 py-3">
                    {{ $batch->product->name ?? 'Không xác định' }}
                </td>

                <td class="px-4 py-3">
                    {{ $batch->harvest_date->format('d/m/Y') }}
                </td>

                <td class="px-4 py-3">
                    {{ $batch->expiry_date->format('d/m/Y') }}
                </td>

                <td class="px-4 py-3 text-right">
                    {{ rtrim(rtrim(number_format($batch->initial_quantity, 2, ',', '.'), '0'), ',') }}
                </td>

                <td class="px-4 py-3 text-right font-semibold">
                    {{ rtrim(rtrim(number_format($batch->current_quantity, 2, ',', '.'), '0'), ',') }}
                    {{ $batch->product->unit ?? '' }}
                </td>

                <td class="px-4 py-3">
                    @include('admin.partials.expiry-badge')
                </td>

                <td class="px-4 py-3">
                    <div class="flex items-center justify-center gap-2">

                        <a href="{{ route('admin.batches.edit', $batch) }}"
                           class="rounded-lg bg-blue-50 px-3 py-2 text-blue-700 hover:bg-blue-100">
                            Sửa
                        </a>

                        <form
                            method="POST"
                            action="{{ route('admin.batches.destroy', $batch) }}"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa lô hàng này?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="rounded-lg bg-red-50 px-3 py-2 text-red-700 hover:bg-red-100">
                                Xóa
                            </button>
                        </form>

                    </div>
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="8"
                    class="px-4 py-6 text-center text-slate-500">
                    Không có lô nào.
                </td>
            </tr>

        @endforelse

        </tbody>
    </table>

</div>

{{-- Phân trang --}}
<div class="mt-4">
    {{ $batches->links() }}
</div>

@endsection

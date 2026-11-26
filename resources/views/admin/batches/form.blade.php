
@extends('admin.layout')

@section('title', $isEdit ? 'Sửa lô hàng' : 'Thêm lô hàng')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.batches.index') }}"
           class="text-sm text-emerald-700 hover:underline">
            &larr; Quay lại danh sách lô
        </a>

        <h1 class="text-2xl font-bold text-stone-800 mt-3">
            {{ $isEdit ? 'Chỉnh sửa lô hàng' : 'Thêm lô hàng mới' }}
        </h1>

        <p class="text-sm text-stone-500 mt-1">
            Quản lý thông tin sản phẩm, ngày thu hoạch và tồn kho.
        </p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-5">
            <p class="font-semibold mb-2">
                Vui lòng kiểm tra lại:
            </p>

            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-6">

        <form
            method="POST"
            action="{{ $isEdit
                ? route('admin.batches.update', $batch)
                : route('admin.batches.store') }}"
        >

            @csrf

            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Sản phẩm --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">
                        Sản phẩm <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="product_id"
                        required
                        class="w-full rounded-lg border-stone-300 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                        <option value="">-- Chọn sản phẩm --</option>

                        @foreach ($products as $product)
                            <option
                                value="{{ $product->id }}"
                                @selected(old('product_id', $batch->product_id) == $product->id)
                            >
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Mã lô --}}
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Mã lô <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="batch_code"
                        value="{{ old('batch_code', $batch->batch_code) }}"
                        required
                        maxlength="50"
                        placeholder="VD: LOT-2026-001"
                        class="w-full rounded-lg border-stone-300 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                </div>

                {{-- Ngày thu hoạch --}}
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Ngày thu hoạch <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="date"
                        name="harvest_date"
                        value="{{ old('harvest_date', $batch->harvest_date?->format('Y-m-d')) }}"
                        required
                        class="w-full rounded-lg border-stone-300 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                </div>

                {{-- Hạn sử dụng --}}
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Hạn sử dụng <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="date"
                        name="expiry_date"
                        value="{{ old('expiry_date', $batch->expiry_date?->format('Y-m-d')) }}"
                        required
                        class="w-full rounded-lg border-stone-300 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                </div>

                {{-- Số lượng ban đầu --}}
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Số lượng ban đầu <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="number"
                        name="initial_quantity"
                        value="{{ old('initial_quantity', $batch->initial_quantity) }}"
                        required
                        min="0.01"
                        step="0.01"
                        class="w-full rounded-lg border-stone-300 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                </div>

                {{-- Số lượng hiện tại: chỉ hiện khi sửa --}}
                @if ($isEdit)
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Tồn kho hiện tại <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="number"
                            name="current_quantity"
                            value="{{ old('current_quantity', $batch->current_quantity) }}"
                            required
                            min="0"
                            step="0.01"
                            class="w-full rounded-lg border-stone-300 focus:border-emerald-500 focus:ring-emerald-500"
                        >

                        <p class="text-xs text-amber-600 mt-1">
                            Lưu ý: chỉnh sửa tồn kho tại đây không tự tạo lịch sử nhập/xuất.
                        </p>
                    </div>
                @endif

                {{-- Trạng thái --}}
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Trạng thái
                    </label>

                    <select
                        name="status"
                        required
                        class="w-full rounded-lg border-stone-300 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                        @php
                            $statuses = [
                                'available' => 'Đang bán',
                                'near_expiry' => 'Cận hạn',
                                'expired' => 'Hết hạn',
                                'empty' => 'Hết hàng',
                            ];
                        @endphp

                        @foreach ($statuses as $key => $label)
                            <option
                                value="{{ $key }}"
                                @selected(old('status', $batch->status ?? 'available') == $key)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-8">

                <a
                    href="{{ route('admin.batches.index') }}"
                    class="px-5 py-2.5 rounded-lg border border-stone-300 text-stone-700 hover:bg-stone-50"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 font-medium"
                >
                    {{ $isEdit ? 'Lưu thay đổi' : 'Tạo lô hàng' }}
                </button>

            </div>

        </form>

    </div>

</div>

@endsection

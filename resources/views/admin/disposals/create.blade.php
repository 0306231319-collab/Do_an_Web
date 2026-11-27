@extends('admin.layout')

@section('title', 'Xuất hủy lô hàng')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="mb-1 text-2xl font-bold">Xuất hủy lô hàng</h1>
            <p class="text-sm text-slate-500">Ghi nhận số lượng hàng hư hỏng cần xuất hủy khỏi kho.</p>
        </div>

        <a href="{{ route('admin.batches.index') }}"
           class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 font-semibold text-slate-700 hover:bg-slate-50">
            ← Quay lại
        </a>
    </div>

    {{-- Hiển thị lỗi validation --}}
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <strong>Vui lòng kiểm tra lại thông tin:</strong>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        // Viền đỏ khi ô có lỗi, viền xám khi bình thường
        $border = [];
        foreach (['batch_id', 'quantity', 'reason'] as $field) {
            $border[$field] = $errors->has($field) ? 'border-red-400' : 'border-slate-300';
        }
    @endphp

    <div class="rounded-2xl border-2 border-emerald-100 bg-white p-5 shadow-sm md:p-6">
        <form action="{{ route('admin.disposals.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Chọn lô hàng --}}
            <div>
                <label for="batch_id" class="mb-1 block text-sm font-semibold text-slate-700">
                    Lô hàng <span class="text-red-600">*</span>
                </label>
                <select name="batch_id" id="batch_id" required
                        class="min-h-11 w-full rounded-lg border px-3 {{ $border['batch_id'] }} focus:border-emerald-600 focus:outline-none">
                    <option value="">-- Chọn lô hàng --</option>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->id }}"
                                data-stock="{{ $batch->current_quantity }}"
                                {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                            {{ $batch->batch_code }}
                            - {{ $batch->product->name ?? 'Sản phẩm' }}
                            - Tồn: {{ $batch->current_quantity }}
                            - HSD: {{ $batch->expiry_date->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
                @error('batch_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hiển thị tồn kho --}}
            <div id="stock-info" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                Vui lòng chọn lô hàng để xem số lượng tồn kho.
            </div>

            {{-- Số lượng hủy --}}
            <div>
                <label for="quantity" class="mb-1 block text-sm font-semibold text-slate-700">
                    Số lượng hủy <span class="text-red-600">*</span>
                </label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}"
                       min="0.01" step="0.01" required
                       class="min-h-11 w-full rounded-lg border px-3 {{ $border['quantity'] }} focus:border-emerald-600 focus:outline-none sm:w-48">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Số lượng hủy không được vượt quá tồn kho hiện tại.</p>
            </div>

            {{-- Lý do xuất hủy --}}
            <div>
                <label for="reason" class="mb-1 block text-sm font-semibold text-slate-700">
                    Lý do xuất hủy <span class="text-red-600">*</span>
                </label>
                <textarea name="reason" id="reason" rows="3" maxlength="255" required
                          placeholder="Ví dụ: Rau dập nát do vận chuyển"
                          class="w-full rounded-lg border px-3 py-2 {{ $border['reason'] }} focus:border-emerald-600 focus:outline-none">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ảnh minh chứng --}}
            <div>
                <label for="image" class="mb-1 block text-sm font-semibold text-slate-700">Ảnh minh chứng</label>
                <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                       class="block w-full rounded-lg border border-dashed border-emerald-300 bg-emerald-50/50 p-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-emerald-700 file:px-3 file:py-2 file:font-semibold file:text-white">
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Chỉ nhận JPG, JPEG, PNG. Dung lượng tối đa 5MB.</p>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                <button type="submit"
                        class="min-h-11 rounded-lg bg-orange-600 px-5 font-semibold text-white hover:bg-orange-700">
                    Xác nhận xuất hủy
                </button>
                <a href="{{ route('admin.batches.index') }}"
                   class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 px-5 font-semibold text-slate-700 hover:bg-slate-50">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const batchSelect = document.getElementById('batch_id');
    const stockInfo = document.getElementById('stock-info');

    function updateStockInfo() {
        const selected = batchSelect.options[batchSelect.selectedIndex];
        if (!selected.value) {
            stockInfo.textContent = 'Vui lòng chọn lô hàng để xem số lượng tồn kho.';
            return;
        }
        const stock = selected.dataset.stock;
        stockInfo.textContent = 'Số lượng tồn kho hiện tại: ' + stock;
    }

    batchSelect.addEventListener('change', updateStockInfo);
    updateStockInfo();
</script>
@endsection

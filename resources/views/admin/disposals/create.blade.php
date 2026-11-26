
@extends('admin.layout')

@section('title', 'Xuất hủy lô hàng')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Xuất hủy lô hàng</h1>
            <p class="text-muted mb-0">
                Ghi nhận số lượng hàng hóa cần xuất hủy khỏi kho.
            </p>
        </div>

        <a href="{{ route('admin.batches.index') }}"
           class="btn btn-secondary">
            Quay lại
        </a>
    </div>

    {{-- Hiển thị lỗi validation --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Vui lòng kiểm tra lại thông tin:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.disposals.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                {{-- Chọn lô hàng --}}
                <div class="mb-3">
                    <label for="batch_id" class="form-label">
                        Lô hàng <span class="text-danger">*</span>
                    </label>

                    <select name="batch_id"
                            id="batch_id"
                            class="form-select @error('batch_id') is-invalid @enderror"
                            required>
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
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Hiển thị tồn kho --}}
                <div class="alert alert-info" id="stock-info">
                    Vui lòng chọn lô hàng để xem số lượng tồn kho.
                </div>

                {{-- Số lượng hủy --}}
                <div class="mb-3">
                    <label for="quantity" class="form-label">
                        Số lượng hủy <span class="text-danger">*</span>
                    </label>

                    <input type="number"
                           name="quantity"
                           id="quantity"
                           class="form-control @error('quantity') is-invalid @enderror"
                           value="{{ old('quantity') }}"
                           min="0.01"
                           step="0.01"
                           required>

                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <small class="text-muted">
                        Số lượng hủy không được vượt quá tồn kho hiện tại.
                    </small>
                </div>

                {{-- Lý do xuất hủy --}}
                <div class="mb-3">
                    <label for="reason" class="form-label">
                        Lý do xuất hủy <span class="text-danger">*</span>
                    </label>

                    <textarea name="reason"
                              id="reason"
                              rows="3"
                              maxlength="255"
                              class="form-control @error('reason') is-invalid @enderror"
                              placeholder="Nhập lý do xuất hủy..."
                              required>{{ old('reason') }}</textarea>

                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ảnh minh chứng --}}
                <div class="mb-4">
                    <label for="image" class="form-label">
                        Ảnh minh chứng
                    </label>

                    <input type="file"
                           name="image"
                           id="image"
                           accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                           class="form-control @error('image') is-invalid @enderror">

                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <small class="text-muted">
                        Chỉ nhận JPG, JPEG, PNG. Dung lượng tối đa 5MB.
                    </small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">
                        Xác nhận xuất hủy
                    </button>

                    <a href="{{ route('admin.batches.index') }}"
                       class="btn btn-outline-secondary">
                        Hủy
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    const batchSelect = document.getElementById('batch_id');
    const stockInfo = document.getElementById('stock-info');

    function updateStockInfo() {
        const selected = batchSelect.options[batchSelect.selectedIndex];

        if (!selected.value) {
            stockInfo.textContent =
                'Vui lòng chọn lô hàng để xem số lượng tồn kho.';
            return;
        }

        const stock = selected.dataset.stock;

        stockInfo.textContent =
            'Số lượng tồn kho hiện tại: ' + stock;
    }

    batchSelect.addEventListener('change', updateStockInfo);

    updateStockInfo();
</script>
@endsection

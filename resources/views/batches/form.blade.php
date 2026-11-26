@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">{{ $batch->exists ? 'Sửa lô hàng' : 'Thêm lô hàng' }}</h1>

    @if ($errors->any())
        <div class="mb-3 p-3 bg-red-100 text-red-800 rounded">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $batch->exists ? route('batches.update', $batch) : route('batches.store') }}" class="space-y-3">
        @csrf
        @if ($batch->exists) @method('PUT') @endif

        <div>
            <label>Sản phẩm</label>
            <select name="product_id" class="w-full border rounded p-2">
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" @selected(old('product_id', $batch->product_id) == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Mã lô</label>
            <input name="batch_code" value="{{ old('batch_code', $batch->batch_code) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label>Ngày thu hoạch</label>
            <input type="date" name="harvest_date" value="{{ old('harvest_date', optional($batch->harvest_date)->format('Y-m-d')) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label>Hạn sử dụng</label>
            <input type="date" name="expiry_date" value="{{ old('expiry_date', optional($batch->expiry_date)->format('Y-m-d')) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label>Số lượng</label>
            <input type="number" min="0" name="quantity" value="{{ old('quantity', $batch->quantity) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label>Trạng thái</label>
            <input name="status" value="{{ old('status', $batch->status ?? 'active') }}" class="w-full border rounded p-2">
        </div>
        <button class="px-4 py-2 bg-green-600 text-white rounded">Lưu</button>
        <a href="{{ route('batches.index') }}" class="px-4 py-2">Hủy</a>
    </form>
</div>
@endsection
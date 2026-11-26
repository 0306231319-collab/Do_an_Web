@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto p-4" x-data="{ preview: null }">
    <h1 class="text-2xl font-bold mb-4">Xuất hủy lô hàng</h1>

    @if ($errors->any())
        <div class="mb-3 p-3 bg-red-100 text-red-800 rounded">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('disposals.store') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <div>
            <label>Chọn lô</label>
            <select name="batch_id" class="w-full border rounded p-2">
                @foreach ($batches as $b)
                    <option value="{{ $b->id }}">{{ $b->batch_code }} (tồn {{ $b->quantity }}, HSD {{ $b->expiry_date->format('d/m/Y') }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Số lượng hủy</label>
            <input type="number" min="1" name="quantity" value="{{ old('quantity') }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label>Lý do</label>
            <select name="reason" class="w-full border rounded p-2">
                <option>Hết hạn sử dụng</option>
                <option>Hư hỏng</option>
                <option>Dập nát khi vận chuyển</option>
                <option>Khác</option>
            </select>
        </div>
        <div>
            <label>Ảnh minh chứng (JPG/PNG, tối đa 5MB)</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png"
                   @change="preview = URL.createObjectURL($event.target.files[0])">
            <img x-show="preview" :src="preview" class="mt-2 h-32 rounded border">
        </div>
        <button class="px-4 py-2 bg-red-600 text-white rounded">Xác nhận xuất hủy</button>
        <a href="{{ route('batches.index') }}" class="px-4 py-2">Hủy</a>
    </form>
</div>
@endsection
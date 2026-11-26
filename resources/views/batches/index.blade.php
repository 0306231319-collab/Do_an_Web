@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto p-4">
    @if (session('success'))
        <div class="mb-3 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Quản lý lô hàng</h1>
        <div class="space-x-2">
            <a href="{{ route('disposals.create') }}" class="px-3 py-2 bg-red-600 text-white rounded">Xuất hủy</a>
            <a href="{{ route('batches.create') }}" class="px-3 py-2 bg-green-600 text-white rounded">Thêm lô</a>
        </div>
    </div>

    <form method="POST" action="{{ route('batches.warning') }}" class="mb-4 flex items-center gap-2">
        @csrf
        <label>Cảnh báo khi còn ≤</label>
        <input type="number" name="days" value="{{ $warningDays }}" min="1" class="w-20 border rounded p-1">
        <span>ngày</span>
        <button class="px-3 py-1 bg-gray-700 text-white rounded">Lưu</button>
    </form>

    <table class="w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">Mã lô</th>
                <th class="p-2 text-left">Sản phẩm</th>
                <th class="p-2">Ngày thu hoạch</th>
                <th class="p-2">Hạn sử dụng</th>
                <th class="p-2">Tồn</th>
                <th class="p-2">Trạng thái</th>
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
        @foreach ($batches as $b)
            <tr class="border-t {{ $b->isNearExpiry() ? 'bg-yellow-50' : '' }}">
                <td class="p-2">{{ $b->batch_code }}</td>
                <td class="p-2">{{ $b->product->name ?? '-' }}</td>
                <td class="p-2 text-center">{{ $b->harvest_date->format('d/m/Y') }}</td>
                <td class="p-2 text-center">{{ $b->expiry_date->format('d/m/Y') }}</td>
                <td class="p-2 text-center">{{ $b->quantity }}</td>
                <td class="p-2 text-center">
                    {{ $b->status }}
                    @if ($b->isNearExpiry())
                        <span class="ml-1 px-2 py-0.5 bg-red-500 text-white rounded text-xs">Sắp hết hạn</span>
                    @endif
                </td>
                <td class="p-2 text-center space-x-2">
                    <a href="{{ route('batches.edit', $b) }}" class="text-blue-600">Sửa</a>
                    <form method="POST" action="{{ route('batches.destroy', $b) }}" class="inline"
                          onsubmit="return confirm('Xóa lô này?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $batches->links() }}</div>
</div>
@endsection
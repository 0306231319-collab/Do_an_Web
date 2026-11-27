@extends('layouts.app')
@section('title', 'Đơn hàng và khiếu nại - Sales')

@php
    $orderBadge = [
        'ChoDuyet' => 'bg-amber-100 text-amber-800', 'DaXacNhan' => 'bg-blue-100 text-blue-800',
        'DangGiao' => 'bg-indigo-100 text-indigo-800', 'DaGiao' => 'bg-green-100 text-green-800',
        'DaHuy' => 'bg-gray-200 text-gray-700',
    ];
    $orderLabel = [
        'ChoDuyet' => 'Chờ duyệt', 'DaXacNhan' => 'Đã xác nhận', 'DangGiao' => 'Đang giao',
        'DaGiao' => 'Đã giao', 'DaHuy' => 'Đã hủy',
    ];
    $reasonLabel = ['DapNat' => 'Hàng dập nát', 'HuHong' => 'Hàng hư hỏng', 'Khac' => 'Lý do khác'];
    $cBadge = [
        'ChoDuyet' => 'bg-amber-100 text-amber-800', 'HoanTien' => 'bg-green-100 text-green-800',
        'GiaoBu' => 'bg-blue-100 text-blue-800', 'TuChoi' => 'bg-red-100 text-red-800',
    ];
    $cLabel = ['ChoDuyet' => 'Chờ xử lý', 'HoanTien' => 'Đã hoàn tiền', 'GiaoBu' => 'Giao bù', 'TuChoi' => 'Đã từ chối'];
    $onlyComplaint = request('loc') === 'khieu-nai';
@endphp

@section('content')
<x-container class="py-8">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-green-800">Đơn hàng và khiếu nại</h1>
            <p class="text-sm text-gray-600">Xem khiếu nại của khách và xử lý: hoàn tiền, giao bù hoặc từ chối (bắt buộc ghi lý do).</p>
        </div>
        <div class="flex gap-2 text-sm font-semibold">
            <a href="{{ route('sales.orders') }}"
               class="rounded-full px-4 py-2 {{ $onlyComplaint ? 'bg-white text-gray-600 border border-green-200' : 'bg-green-700 text-white' }}">Tất cả đơn</a>
            <a href="{{ route('sales.orders', ['loc' => 'khieu-nai']) }}"
               class="rounded-full px-4 py-2 {{ $onlyComplaint ? 'bg-green-700 text-white' : 'bg-white text-gray-600 border border-green-200' }}">Chỉ đơn có khiếu nại</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-green-300 bg-green-50 p-4 text-sm font-medium text-green-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            @foreach ($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($orders as $order)
            @php $c = $order->complaint; @endphp
            <div class="rounded-2xl bg-white p-5 shadow-sm border border-green-200">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="font-bold text-gray-900">Đơn #{{ $order->id }}</span>
                        <span class="ml-2 text-sm text-gray-500">{{ $order->user->full_name ?? $order->user->email ?? 'Khách' }}</span>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $orderBadge[$order->trang_thai] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $orderLabel[$order->trang_thai] ?? $order->trang_thai }}
                    </span>
                </div>

                @if ($c)
                    <div class="mt-4 grid gap-4 border-t pt-4 md:grid-cols-3">
                        <div class="md:col-span-2">
                            <div class="mb-2 flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-700">Khiếu nại:</span>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $cBadge[$c->trang_thai] ?? 'bg-gray-100' }}">
                                    {{ $cLabel[$c->trang_thai] ?? $c->trang_thai }}
                                </span>
                            </div>
                            <p class="text-sm"><strong>Lý do:</strong> {{ $reasonLabel[$c->ly_do] ?? $c->ly_do }}</p>
                            @if ($c->mo_ta)<p class="mt-1 text-sm text-gray-600"><strong>Mô tả:</strong> {{ $c->mo_ta }}</p>@endif
                            @if ($c->ly_do_tu_choi)
                                <p class="mt-1 text-sm text-gray-600"><strong>Lý do xử lý:</strong> {{ $c->ly_do_tu_choi }}</p>
                            @endif
                            @if ($c->hinh_anh_minh_chung)
                                <a href="{{ asset('storage/' . $c->hinh_anh_minh_chung) }}" target="_blank" class="mt-2 inline-block">
                                    <img src="{{ asset('storage/' . $c->hinh_anh_minh_chung) }}" alt="Ảnh minh chứng"
                                         class="h-24 w-24 rounded-lg object-cover border border-green-200" onerror="this.remove()">
                                </a>
                            @endif
                        </div>

                        <div>
                            @if ($c->trang_thai === 'ChoDuyet')
                                <form action="{{ route('sales.complaints.resolve', $c->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    <select name="trang_thai" required
                                            class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none">
                                        <option value="HoanTien">Hoàn tiền</option>
                                        <option value="GiaoBu">Giao bù</option>
                                        <option value="TuChoi">Từ chối</option>
                                    </select>
                                    <input type="text" name="ly_do_xu_ly" required minlength="5" maxlength="255"
                                           placeholder="Lý do xử lý (bắt buộc)"
                                           class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none">
                                    <button type="submit"
                                            class="w-full rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                        Lưu xử lý
                                    </button>
                                </form>
                            @else
                                <p class="text-sm text-gray-500">Đã xử lý{{ $c->ngay_duyet ? ' lúc ' . \Carbon\Carbon::parse($c->ngay_duyet)->format('d/m/Y H:i') : '' }}.</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="mt-3 text-sm text-gray-400">Chưa có khiếu nại.</p>
                @endif
            </div>
        @empty
            <p class="rounded-2xl bg-white p-8 text-center text-gray-500">Chưa có đơn hàng nào.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</x-container>
@endsection

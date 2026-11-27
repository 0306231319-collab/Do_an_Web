@extends('layouts.app')
@section('title', 'Gửi khiếu nại đơn hàng')
@section('content')
<div class="mx-auto max-w-xl px-4 py-10">
    <a href="/" class="text-sm text-green-700 hover:underline">← Về trang chủ</a>
    <h1 class="mt-2 text-2xl font-extrabold text-green-800">Khiếu nại đơn hàng #{{ $order->id }}</h1>
    <p class="mb-5 mt-1 text-sm text-gray-600">
        Chỉ áp dụng cho đơn đã giao, trong vòng 24 giờ kể từ khi nhận hàng. Mỗi đơn gửi một khiếu nại.
    </p>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-green-300 bg-green-50 p-4 text-sm font-medium text-green-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            @foreach ($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    @endif

    @include('orders.complaint_form', ['order' => $order])
</div>
@endsection

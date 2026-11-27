
@extends('layouts.app')

@section('title', 'Đặt hàng thành công - Nông Sản Sạch')

@section('content')
<x-container class="py-16">
    <div class="mx-auto max-w-xl rounded-2xl bg-white p-8 text-center shadow-sm">
        <div class="mb-5 text-5xl text-green-600">✓</div>

        <h1 class="mb-3 text-2xl font-bold text-green-700">
            Đặt hàng thành công!
        </h1>

        <p class="mb-4 text-gray-600">
            Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đã được ghi nhận.
        </p>

        @if (request('order'))
            <p class="mb-6">
                Mã đơn hàng:
                <strong class="text-green-700">{{ request('order') }}</strong>
            </p>
        @endif

        <p class="mb-6 text-sm text-gray-500">
            Bạn sẽ thanh toán khi nhận được hàng (COD).
        </p>

        <a
            href="/"
            class="inline-flex rounded-lg bg-green-700 px-6 py-3 font-semibold text-white"
        >
            Tiếp tục mua sắm
        </a>
    </div>
</x-container>
@endsection

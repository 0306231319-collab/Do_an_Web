@extends('layouts.app')
@section('title', 'Về chúng tôi - Nông Sản Sạch')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10 space-y-10">

    {{-- Banner --}}
    <section class="rounded-2xl bg-green-800 text-white px-8 py-12 shadow-md">
        <p class="text-green-200 text-sm font-semibold uppercase tracking-wide">Về chúng tôi</p>
        <h1 class="mt-2 text-3xl md:text-4xl font-bold">Nông sản sạch từ trang trại đến bàn ăn</h1>
        <p class="mt-4 max-w-2xl text-green-100">
            Chúng tôi là một trang trại duy nhất (Single Farm), tự trồng, tự thu hoạch và tự giao hàng.
            Mỗi lô hàng đều có mã truy xuất nguồn gốc để bạn biết rõ rau củ mình ăn đến từ đâu.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ Route::has('products.index') ? route('products.index') : '#' }}"
               class="inline-block rounded-lg bg-white text-green-800 font-semibold px-5 py-2.5 shadow hover:bg-green-50">Xem sản phẩm</a>
            <a href="{{ Route::has('subscriptions.index') ? route('subscriptions.index') : '#' }}"
               class="inline-block rounded-lg border border-white/70 text-white font-semibold px-5 py-2.5 hover:bg-green-700">Gói định kỳ</a>
        </div>
    </section>

    {{-- Giá trị cốt lõi --}}
    <section>
        <h2 class="text-2xl font-bold text-green-900">Vì sao chọn chúng tôi</h2>
        <div class="mt-5 grid gap-5 md:grid-cols-3">
            @foreach([
                ['leaf','Sạch & hữu cơ','Không thuốc bảo vệ thực vật cấm, canh tác theo quy trình VietGAP, có nhật ký từng lô.'],
                ['box','Xuất kho FEFO','Hệ thống ưu tiên lô có hạn gần nhất, bạn luôn nhận hàng tươi và không bị lô hết hạn.'],
                ['search','Truy xuất nguồn gốc','Quét mã QR trên sản phẩm để xem ngày thu hoạch, hạn dùng và lô hàng.'],
            ] as [$icon, $title, $desc])
                <div class="bg-white rounded-xl border border-green-200 shadow-sm p-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-700">
                    <x-icon :name="$icon" class="h-7 w-7" />
                </div>
                    <h3 class="mt-3 text-lg font-semibold text-green-800">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Thống kê --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([['100%','Có mã truy xuất'],['4 kỳ','Giao mỗi gói tháng'],['24h','Khiếu nại sau giao'],['FEFO','Xuất kho thông minh']] as [$n,$l])
            <div class="bg-white rounded-xl border border-green-200 shadow-sm p-5 text-center">
                <div class="text-2xl font-bold text-green-700">{{ $n }}</div>
                <div class="mt-1 text-sm text-gray-600">{{ $l }}</div>
            </div>
        @endforeach
    </section>

    {{-- Nhóm thực hiện --}}
    <section>
        <h2 class="text-2xl font-bold text-green-900">Nhóm thực hiện (Nhóm 10)</h2>
        <p class="mt-1 text-sm text-gray-600">Đồ án 11 - Website TMĐT Nông sản sạch, Cao đẳng Kỹ thuật Cao Thắng.</p>
        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @foreach([
                ['Nhơn','Nhóm trưởng'],
                ['Nam','Xác thực & Khiếu nại'],
                ['Nhân','FEFO & Gói định kỳ'],
                ['Trung','API & Phân quyền'],
                ['Bảo','Truy xuất & Trang giới thiệu'],
            ] as [$name, $role])
                <div class="bg-white rounded-xl border border-green-200 shadow-sm p-5 text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-green-700 text-white flex items-center justify-center text-lg font-bold">{{ mb_substr($name,0,1) }}</div>
                    <div class="mt-3 font-semibold text-gray-800">{{ $name }}</div>
                    <div class="text-xs text-gray-500">{{ $role }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Liên hệ --}}
    <section class="bg-white rounded-xl border border-green-200 shadow-sm p-6">
        <h2 class="text-xl font-bold text-green-900">Liên hệ</h2>
        <ul class="mt-3 space-y-1 text-sm text-gray-700">
            <li class="flex items-center gap-2"><x-icon name="pin" class="h-5 w-5 text-green-700" /> Trang trại Nông Sản Sạch, TP. Hồ Chí Minh</li>
            <li class="flex items-center gap-2"><x-icon name="phone" class="h-5 w-5 text-green-700" /> 0900 000 000</li>
            <li class="flex items-center gap-2"><x-icon name="mail" class="h-5 w-5 text-green-700" /> lienhe@nongsansach.test</li>
        </ul>
    </section>
</div>
@endsection

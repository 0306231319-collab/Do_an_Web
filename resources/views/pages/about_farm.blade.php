@extends('layouts.app')

@section('title', 'Giới thiệu Nông trại')
@section('show_header', true)
@section('show_footer', true)

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 space-y-8">
    <section class="rounded-2xl bg-[#1E5631] p-6 md:p-10 text-white">
        <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">Single Farm · Canh tác bền vững</span>
        <h1 class="mt-3 text-2xl md:text-4xl font-bold">Nông Trại Xanh Đà Lạt — Từ vườn đến bàn ăn</h1>
        <p class="mt-3 max-w-2xl text-sm md:text-base text-emerald-100 leading-relaxed">
            Mô hình nông trại khép kín áp dụng quy trình VietGAP, ghi chép nhật ký canh tác điện tử theo từng lô thu hoạch và xuất kho ưu tiên độ tươi mới (FEFO).
        </p>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-[#E6E3D8] bg-white p-5">
            <h2 class="text-base font-bold text-[#1E5631]">1. Canh tác hữu cơ chuẩn VietGAP</h2>
            <p class="mt-2 text-sm text-[#4B5563]">Không sử dụng thuốc bảo vệ thực vật hóa học tồn dư, sử dụng phân bón hữu cơ ủ vi sinh và hệ thống tưới nhỏ giọt tự động.</p>
        </div>
        <div class="rounded-2xl border border-[#E6E3D8] bg-white p-5">
            <h2 class="text-base font-bold text-[#1E5631]">2. Minh bạch truy xuất mã QR</h2>
            <p class="mt-2 text-sm text-[#4B5563]">Mỗi lô thu hoạch đều được cấp mã QR riêng, lưu trữ toàn bộ lịch sử gieo trồng, chăm sóc, hình ảnh thực tế tại vườn.</p>
        </div>
        <div class="rounded-2xl border border-[#E6E3D8] bg-white p-5">
            <h2 class="text-base font-bold text-[#1E5631]">3. Thu hoạch & Giao trong ngày</h2>
            <p class="mt-2 text-sm text-[#4B5563]">Kiểm soát hạn sử dụng nghiêm ngặt, tự động cảnh báo xả hàng và xuất hủy hàng hư hỏng để đảm bảo chất lượng tốt nhất.</p>
        </div>
    </section>
</div>
@endsection
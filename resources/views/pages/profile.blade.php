@extends('layouts.app')
@section('title', 'Thông tin cá nhân - Nông Sản Sạch')

@php
    $roleLabels = ['admin' => 'Quản trị viên', 'farm_staff' => 'Nhân viên nông trại', 'sales' => 'Nhân viên bán hàng', 'customer' => 'Khách hàng'];
    $statusLabels = ['ChoDuyet' => 'Chờ duyệt', 'DaXacNhan' => 'Đã xác nhận', 'DangGiao' => 'Đang giao', 'DaGiao' => 'Đã giao', 'DaHuy' => 'Đã hủy'];
    $statusColors = [
        'ChoDuyet' => 'bg-amber-50 text-amber-700 border-amber-200',
        'DaXacNhan' => 'bg-blue-50 text-blue-700 border-blue-200',
        'DangGiao' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'DaGiao' => 'bg-green-50 text-green-700 border-green-200',
        'DaHuy' => 'bg-red-50 text-red-700 border-red-200',
    ];
    $initial = mb_strtoupper(mb_substr($user->full_name ?? 'U', 0, 1));
@endphp

@section('content')
<div class="mx-auto max-w-6xl px-4 py-10">
    <h1 class="mb-6 flex items-center gap-3 text-3xl font-bold text-green-900">
        <x-icon name="user" class="h-8 w-8 text-green-700" /> Thông tin cá nhân
    </h1>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-green-300 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Thẻ tài khoản --}}
        <aside class="rounded-2xl border border-green-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col items-center text-center">
                <span class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-green-100 bg-green-700 text-3xl font-bold text-white">{{ $initial }}</span>
                <p class="mt-3 text-lg font-bold text-green-900">{{ $user->full_name }}</p>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <span class="mt-3 rounded-full bg-green-700 px-3 py-1 text-xs font-semibold text-white">{{ $roleLabels[$user->role] ?? $user->role }}</span>
            </div>

            @if ($user->role === 'customer')
                <div class="mt-5 grid grid-cols-2 gap-3 text-center">
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
                        <div class="flex items-center justify-center gap-1 text-xl font-bold text-amber-700"><x-icon name="star" class="h-5 w-5" /> {{ number_format($user->loyalty_points) }}</div>
                        <div class="text-xs text-gray-600">Điểm tích lũy</div>
                    </div>
                    <div class="rounded-xl border border-green-200 bg-green-50 p-3">
                        <div class="text-xl font-bold text-green-700">{{ $orderCount }}</div>
                        <div class="text-xs text-gray-600">Đơn hàng</div>
                    </div>
                </div>
                <p class="mt-3 text-center text-xs text-gray-500">Mỗi 10.000 đồng mua hàng được 1 điểm khi đơn được giao.</p>
            @endif

            <div class="mt-5 grid gap-2 text-sm">
                @if (Route::has('subscriptions.index'))
                    <a href="{{ route('subscriptions.index') }}" class="flex items-center gap-2 rounded-lg border border-green-200 px-3 py-2 font-medium text-green-800 hover:bg-green-50"><x-icon name="calendar" class="h-5 w-5" /> Gói định kỳ của tôi</a>
                @endif
                <a href="{{ route('cart.index') }}" class="flex items-center gap-2 rounded-lg border border-green-200 px-3 py-2 font-medium text-green-800 hover:bg-green-50"><x-icon name="cart" class="h-5 w-5" /> Giỏ hàng</a>
            </div>
        </aside>

        <div class="space-y-6 lg:col-span-2">

            {{-- Cập nhật thông tin --}}
            <section class="rounded-2xl border border-green-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-green-900">Cập nhật thông tin</h2>
                <form method="POST" action="{{ route('profile.update') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PUT')

                    <label class="grid gap-1 text-sm font-semibold text-gray-700">Họ và tên
                        <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                               class="min-h-11 rounded-lg border border-green-300 px-3 font-normal focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-200">
                        @error('full_name') <span class="text-xs font-normal text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-1 text-sm font-semibold text-gray-700">Số điện thoại
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                               class="min-h-11 rounded-lg border border-green-300 px-3 font-normal focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-200">
                        @error('phone') <span class="text-xs font-normal text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-1 text-sm font-semibold text-gray-700 sm:col-span-2">Email (không thể đổi)
                        <input type="email" value="{{ $user->email }}" disabled
                               class="min-h-11 rounded-lg border border-gray-200 bg-gray-50 px-3 font-normal text-gray-500">
                    </label>

                    <div class="sm:col-span-2">
                        <button class="rounded-lg bg-green-700 px-6 py-2.5 font-semibold text-white shadow hover:bg-green-800">Lưu thay đổi</button>
                    </div>
                </form>
            </section>

            {{-- Đơn hàng gần đây --}}
            <section class="rounded-2xl border border-green-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-green-900">Đơn hàng gần đây</h2>
                @if ($orders->isEmpty())
                    <p class="rounded-xl border border-dashed border-green-200 bg-green-50 px-4 py-6 text-center text-sm text-gray-600">Bạn chưa có đơn hàng nào.</p>
                @else
                    <div class="overflow-x-auto rounded-xl border border-green-200">
                        <table class="w-full min-w-[620px] text-sm">
                            <thead class="bg-green-100 text-left text-green-900">
                                <tr>
                                    <th class="p-3">Mã đơn</th>
                                    <th class="p-3">Ngày đặt</th>
                                    <th class="p-3">Tổng tiền</th>
                                    <th class="p-3">Trạng thái</th>
                                    <th class="p-3">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $o)
                                    <tr class="border-t border-green-100 hover:bg-green-50">
                                        <td class="p-3 font-semibold">{{ $o->ma_don_hang }}</td>
                                        <td class="p-3">{{ optional($o->ngay_tao)->format('d/m/Y H:i') }}</td>
                                        <td class="p-3">{{ number_format($o->tong_tien, 0, ',', '.') }}đ</td>
                                        <td class="p-3">
                                            <span class="rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusColors[$o->trang_thai] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                                {{ $statusLabels[$o->trang_thai] ?? $o->trang_thai }}
                                            </span>
                                        </td>
                                        <td class="p-3">
                                            @if ($o->trang_thai === 'DaGiao' && $o->ngay_giao_thuc_te && $o->ngay_giao_thuc_te->gt(now()->subDay()) && Route::has('complaints.create'))
                                                <a href="{{ route('complaints.create', $o->id) }}" class="rounded-md border border-amber-300 px-3 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">Khiếu nại</a>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection

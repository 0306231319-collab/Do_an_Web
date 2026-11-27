@extends('layouts.app')

@section('title', 'Tài khoản & Đăng nhập - Nông Sản Sạch')

@section('content')
<div class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-md flex-col justify-center px-4 py-10">
    <div class="overflow-hidden rounded-2xl border border-[#E6E3D8] bg-white shadow-sm">
        {{-- Header xanh đậm #1F5C34 --}}
        <div style="background-color: #1F5C34;" class="px-6 py-5 text-white">
            <h1 class="text-xl font-bold">
                @auth
                    Thông tin tài khoản
                @else
                    Đăng nhập hệ thống
                @endauth
            </h1>
            <p class="mt-1 text-xs text-[#D8EADF]">
                Nông sản sạch · Quản lý lô hàng & Truy xuất nguồn gốc
            </p>
        </div>

        <div class="p-6">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-3 text-xs font-medium text-green-800 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @auth
                {{-- Hiển thị khi đã đăng nhập --}}
                <div class="space-y-3 text-sm text-gray-700">
                    <div class="rounded-xl bg-[#F7F6F0] p-4 border border-[#E6E3D8]">
                        <p><span class="text-gray-500">Họ tên:</span> <strong class="text-gray-900">{{ auth()->user()->name }}</strong></p>
                        <p class="mt-1"><span class="text-gray-500">Email:</span> <strong class="text-gray-900">{{ auth()->user()->email }}</strong></p>
                        <p class="mt-1"><span class="text-gray-500">Vai trò:</span>
                            <span class="inline-block rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">
                                {{ auth()->user()->role ?? 'Thành viên' }}
                            </span>
                        </p>
                    </div>

                    <div class="pt-2 flex flex-col gap-2.5">
                        <a href="{{ url('/farm/batches') }}"
                           style="background-color: #1F5C34;"
                           class="w-full rounded-xl py-2.5 text-center text-sm font-semibold text-white transition hover:opacity-90">
                            Quay lại Quản lý lô hàng
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-xl border border-red-600 bg-white py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 cursor-pointer">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Form đăng nhập khi chưa đăng nhập --}}
                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700">Email đăng nhập</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="staff@nongsansach.vn"
                               class="mt-1.5 w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:border-[#1F5C34] focus:outline-none">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-700">Mật khẩu</label>
                        <input id="password" type="password" name="password" required
                               placeholder="••••••••"
                               class="mt-1.5 w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:border-[#1F5C34] focus:outline-none">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#1F5C34]">
                            <span>Ghi nhớ đăng nhập</span>
                        </label>
                        <a href="{{ url('/farm/batches') }}" class="text-[#1F5C34] hover:underline">← Quay lại</a>
                    </div>

                    <button type="submit"
                            style="background-color: #1F5C34;"
                            class="w-full rounded-xl py-2.5 text-sm font-semibold text-white transition hover:opacity-90 cursor-pointer">
                        Đăng nhập
                    </button>
                </form>
            @endauth
        </div>
    </div>
</div>
@endsection
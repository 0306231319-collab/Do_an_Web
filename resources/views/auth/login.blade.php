@extends('layouts.app')
@section('title', 'Đăng nhập - Nông Sản Sạch')

@section('content')
<div class="mx-auto flex min-h-[70vh] max-w-md items-center px-4 py-10">
    <main class="w-full rounded-2xl border border-green-200 bg-white p-8 shadow-md">
        <div class="mb-6 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Nông Sản Sạch" class="mx-auto mb-3 h-16 w-16">
            <h1 class="text-2xl font-bold text-green-900">Đăng nhập</h1>
            <p class="mt-1 text-sm text-gray-500">Chào mừng bạn quay lại Nông Sản Sạch</p>
        </div>

        <form method="POST" action="{{ route('login.attempt') }}" class="grid gap-4">
            @csrf

            <label class="grid gap-1 text-sm font-semibold text-gray-700">Email
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="min-h-11 rounded-lg border border-green-300 px-3 font-normal focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-200">
            </label>

            <label class="grid gap-1 text-sm font-semibold text-gray-700">Mật khẩu
                <input type="password" name="password" required autocomplete="current-password"
                       class="min-h-11 rounded-lg border border-green-300 px-3 font-normal focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-200">
            </label>

            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-green-400 text-green-700"> Ghi nhớ đăng nhập
            </label>

            @error('email')
                <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ $message }}</p>
            @enderror
            @error('password')
                <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ $message }}</p>
            @enderror

            <button class="min-h-11 rounded-lg bg-green-700 font-semibold text-white shadow hover:bg-green-800">Đăng nhập</button>
        </form>

        @if (app()->environment('local'))
            <p class="mt-5 rounded-lg bg-green-50 px-3 py-2 text-xs text-gray-600">
                Tài khoản mẫu (mật khẩu: <b>password</b>): admin@nongsan.test, staff@nongsan.test, sales@nongsan.test, khach1@nongsan.test
            </p>
        @endif
    </main>
</div>
@endsection

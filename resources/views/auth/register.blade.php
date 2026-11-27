@extends('layouts.app')

@section('title', 'Đăng ký')
@section('show_header', true)
@section('show_footer', true)

@section('content')
<div class="mx-auto max-w-md px-4 py-10">
    <div class="rounded-2xl border-2 border-green-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold text-[#111827]">Tạo tài khoản khách hàng</h1>
        <p class="mt-1 text-sm text-[#6B7280]">Đăng ký để đặt hàng và theo dõi đơn.</p>

        <form method="POST" action="{{ route('register.store') }}" class="mt-5 space-y-4">
            @csrf
            @foreach ([
                ['full_name', 'Họ và tên', 'text'],
                ['email', 'Email', 'email'],
                ['phone', 'Số điện thoại', 'text'],
                ['password', 'Mật khẩu', 'password'],
                ['password_confirmation', 'Nhập lại mật khẩu', 'password'],
            ] as [$name, $label, $type])
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">{{ $label }}</label>
                    <input type="{{ $type }}" name="{{ $name }}"
                           value="{{ $type === 'password' ? '' : old($name) }}"
                           class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-[#1E5631] focus:outline-none">
                    @error($name)
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <button class="w-full rounded-xl bg-[#1E5631] py-3 text-sm font-bold text-white hover:bg-[#164225]">
                Đăng ký
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="font-semibold text-[#1E5631] underline">Đăng nhập</a>
        </p>
    </div>
</div>
@endsection

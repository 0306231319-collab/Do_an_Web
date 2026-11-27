<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập · Nông sản sạch</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-stone-50 text-slate-800 flex items-center justify-center p-4">
<main class="w-full max-w-sm rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
    <h1 class="text-2xl font-bold text-emerald-800">Nông sản sạch</h1>
    <p class="mb-5 text-sm text-slate-500">Đăng nhập để tiếp tục</p>

    <form method="POST" action="{{ route('login.attempt') }}" class="grid gap-4">
        @csrf

        <label class="grid gap-1 text-sm font-semibold">Email
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="min-h-11 rounded-lg border border-stone-300 px-3 font-normal">
        </label>

        <label class="grid gap-1 text-sm font-semibold">Mật khẩu
            <input type="password" name="password" required autocomplete="current-password"
                   class="min-h-11 rounded-lg border border-stone-300 px-3 font-normal">
        </label>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" value="1" class="h-5 w-5"> Ghi nhớ đăng nhập
        </label>

        @error('email')
            <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ $message }}</p>
        @enderror
        @error('password')
            <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ $message }}</p>
        @enderror

        <button class="min-h-11 rounded-lg bg-emerald-700 font-semibold text-white hover:bg-emerald-800">Đăng nhập</button>
    </form>

    @if (app()->environment('local'))
        <p class="mt-5 border-t border-stone-200 pt-3 text-xs text-slate-500">
            Tài khoản mẫu (mật khẩu: password): admin@nongsan.test, staff@nongsan.test, sales@nongsan.test, khach1@nongsan.test
        </p>
    @endif
</main>
</body>
</html>

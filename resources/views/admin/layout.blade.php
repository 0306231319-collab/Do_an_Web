<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị') · Nông sản sạch</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-50 text-slate-800">
<div class="min-h-screen md:flex">
    <aside class="bg-emerald-900 text-emerald-50 md:w-60 md:min-h-screen md:shrink-0">
        <div class="px-5 py-4 text-lg font-bold border-b border-emerald-800">Nông sản sạch<span class="block text-xs font-normal text-emerald-200">Khu quản trị</span></div>
        <nav class="flex gap-1 overflow-x-auto p-2 md:block md:space-y-1 md:p-3 text-sm">
            @php
                $links = [
                    ['admin.dashboard', 'Tổng quan', 'admin'],
                    ['admin.batches.index', 'Lô hàng và tồn kho', 'admin/batches*'],
                    ['admin.products.index', 'Sản phẩm', 'admin/products*'],
                    ['admin.orders.index', 'Đơn hàng', 'admin/orders*'],
                    ['admin.customers.index', 'Khách hàng và điểm', 'admin/customers*'],
                ];
            @endphp
            @foreach ($links as [$route, $label, $pattern])
                <a href="{{ route($route) }}"
                   class="whitespace-nowrap rounded-lg px-3 py-2 min-h-11 flex items-center {{ request()->is($pattern) ? 'bg-emerald-700 font-semibold text-white' : 'hover:bg-emerald-800' }}">{{ $label }}</a>
            @endforeach
        </nav>
        <div class="hidden md:block px-5 py-3 text-xs text-emerald-200 border-t border-emerald-800 mt-4">
            {{ auth()->user()->full_name ?? '' }}
                       <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button class="underline">Đăng xuất</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-4 md:p-8 min-w-0">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <p class="font-semibold">Vui lòng kiểm tra lại:</p>
                <ul class="list-disc ml-5 text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>

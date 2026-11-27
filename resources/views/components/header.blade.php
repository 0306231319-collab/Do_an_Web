@php
    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'farm_staff' => 'Nhân viên nông trại',
        'sales'      => 'Nhân viên bán hàng',
        'customer'   => 'Khách hàng',
    ];
    $u = auth()->user();
    $initial = $u ? mb_strtoupper(mb_substr($u->full_name ?? 'U', 0, 1)) : '';
    $navLinks = [
        ['Trang chủ',     '/'],
        ['Sản phẩm',      Route::has('products.index') ? route('products.index') : '#'],
        ['Gói định kỳ',   Route::has('subscriptions.index') ? route('subscriptions.index') : '#'],
        ['Về chúng tôi',  route('about')],
    ];
@endphp

<header x-data="{ menu: false, user: false }" @keydown.escape.window="menu = false; user = false"
        class="sticky top-0 z-40 bg-green-800 text-white shadow-md">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">

        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2 text-xl font-extrabold tracking-tight">
            <img src="{{ asset('images/logo.png') }}" alt="Nông Sản Sạch" class="h-10 w-10">
            <span>Nông Sản Sạch</span>
        </a>

        {{-- Menu máy tính --}}
        <nav class="hidden items-center gap-1 md:flex">
            @foreach ($navLinks as [$label, $url])
                <a href="{{ $url }}"
                   class="rounded-lg px-4 py-2 text-sm font-medium text-green-50 transition hover:bg-green-700 hover:text-white">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            {{-- Giỏ hàng --}}
            <a href="{{ route('cart.index') }}"
               class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-green-800 shadow-sm transition hover:bg-green-50">
                <span class="inline-flex items-center gap-1.5"><x-icon name="cart" class="h-5 w-5" /> <span class="hidden sm:inline">Giỏ hàng</span></span>
            </a>

            @guest
                <a href="{{ route('login') }}"
                   class="rounded-lg border border-white/60 px-4 py-2 text-sm font-semibold transition hover:bg-white/10">
                    Đăng nhập
                </a>
            @endguest

            @auth
                {{-- Nút tài khoản --}}
                <div class="relative">
                    <button type="button" @click="user = !user" @click.outside="user = false"
                            class="flex items-center gap-2 rounded-full bg-green-700 py-1 pl-1 pr-3 transition hover:bg-green-600">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white font-bold text-green-800">{{ $initial }}</span>
                        <span class="hidden max-w-[8rem] truncate text-sm font-medium sm:inline">{{ $u->full_name }}</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5.5 7.5l4.5 4.5 4.5-4.5"/></svg>
                    </button>

                    {{-- Bảng thông tin cá nhân --}}
                    <div x-show="user" x-cloak x-transition
                         class="absolute right-0 mt-3 w-80 overflow-hidden rounded-2xl border-2 border-green-200 bg-white text-gray-800 shadow-2xl">
                        <div class="border-b-2 border-green-100 bg-green-50 p-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-white bg-green-700 text-2xl font-bold text-white shadow">{{ $initial }}</span>
                                <div class="min-w-0">
                                    <p class="truncate text-base font-bold text-green-900">{{ $u->full_name }}</p>
                                    <p class="truncate text-xs text-gray-500">{{ $u->email }}</p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between gap-2 text-xs">
                                <span class="rounded-full bg-green-700 px-3 py-1 font-semibold text-white">
                                    {{ $roleLabels[$u->role] ?? $u->role }}
                                </span>
                                @if ($u->role === 'customer')
                                    <span class="inline-flex items-center gap-1 rounded-full border border-amber-300 bg-amber-50 px-3 py-1 font-semibold text-amber-700">
                                        <x-icon name="star" class="h-4 w-4" /> {{ number_format($u->loyalty_points ?? 0) }} điểm
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid gap-1 p-2 text-sm">
                            @php
                                $itemClass = 'flex items-center gap-3 rounded-lg border border-transparent px-3 py-2.5 font-medium text-gray-700 transition hover:border-green-200 hover:bg-green-50 hover:text-green-800';
                            @endphp
                            @if (Route::has('profile'))
                                <a href="{{ route('profile') }}" class="{{ $itemClass }}"><x-icon name="user" class="h-5 w-5 text-green-700" /> Thông tin cá nhân</a>
                            @endif
                            @if ($u->role === 'admin' && Route::has('admin.dashboard'))
                                <a href="{{ route('admin.dashboard') }}" class="{{ $itemClass }}"><x-icon name="grid" class="h-5 w-5 text-green-700" /> Trang quản trị</a>
                            @endif
                            @if (in_array($u->role, ['farm_staff', 'admin']) && Route::has('batches.index'))
                                <a href="{{ route('batches.index') }}" class="{{ $itemClass }}"><x-icon name="box" class="h-5 w-5 text-green-700" /> Quản lý lô hàng</a>
                            @endif
                            @if (in_array($u->role, ['sales', 'admin']) && Route::has('sales.orders'))
                                <a href="{{ route('sales.orders') }}" class="{{ $itemClass }}"><x-icon name="receipt" class="h-5 w-5 text-green-700" /> Đơn hàng và khiếu nại</a>
                            @endif
                            @if ($u->role === 'customer')
                                @if (Route::has('subscriptions.index'))
                                    <a href="{{ route('subscriptions.index') }}" class="{{ $itemClass }}"><x-icon name="calendar" class="h-5 w-5 text-green-700" /> Gói định kỳ của tôi</a>
                                @endif
                                <a href="{{ route('cart.index') }}" class="{{ $itemClass }}"><x-icon name="cart" class="h-5 w-5 text-green-700" /> Giỏ hàng</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t-2 border-green-100 pt-2">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-lg border border-transparent px-3 py-2.5 text-left font-medium text-red-600 transition hover:border-red-200 hover:bg-red-50">
                                    <x-icon name="logout" class="h-5 w-5" /> Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth

            {{-- Nút menu điện thoại --}}
            <button type="button" @click="menu = !menu" class="rounded-lg p-2 hover:bg-green-700 md:hidden" aria-label="Menu">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Menu điện thoại --}}
    <nav x-show="menu" x-cloak x-transition class="border-t border-green-700 bg-green-900 px-4 py-2 md:hidden">
        @foreach ($navLinks as [$label, $url])
            <a href="{{ $url }}" class="block rounded-lg px-3 py-3 text-sm font-medium hover:bg-green-800">{{ $label }}</a>
        @endforeach
    </nav>
</header>

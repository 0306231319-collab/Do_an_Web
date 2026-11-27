<footer class="mt-16 bg-green-900 text-white">
    {{-- Dải kêu gọi --}}
    <div class="border-b border-green-700 bg-green-800">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-4 px-4 py-6 md:flex-row md:items-center">
            <div>
                <p class="text-lg font-bold">Nông sản tươi mỗi tuần, giao tận nhà</p>
                <p class="text-sm text-green-100">Đăng ký gói định kỳ 1 tháng, 4 kỳ giao, xuất kho theo lô tươi nhất.</p>
            </div>
            <a href="{{ Route::has('subscriptions.index') ? route('subscriptions.index') : '#' }}"
               class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-green-800 shadow hover:bg-green-50">
                <x-icon name="box" class="h-5 w-5" /> Đăng ký gói định kỳ
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="grid grid-cols-1 gap-10 md:grid-cols-4">

            {{-- Thông tin --}}
            <div class="md:col-span-2">
                <h2 class="mb-3 flex items-center gap-3 text-xl font-extrabold">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-11 w-11">
                    Nông Sản Sạch
                </h2>
                <p class="max-w-md text-sm leading-relaxed text-green-100">
                    Mang nông sản sạch, an toàn và minh bạch nguồn gốc đến mọi gia đình.
                    Mỗi lô hàng đều có mã QR truy xuất để bạn biết rõ rau củ đến từ đâu.
                </p>
                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                    <span class="inline-flex items-center gap-1 rounded-full border border-green-600 px-3 py-1"><x-icon name="check" class="h-4 w-4" /> Có mã truy xuất</span>
                    <span class="inline-flex items-center gap-1 rounded-full border border-green-600 px-3 py-1"><x-icon name="leaf" class="h-4 w-4" /> Canh tác sạch</span>
                    <span class="inline-flex items-center gap-1 rounded-full border border-green-600 px-3 py-1"><x-icon name="box" class="h-4 w-4" /> Xuất kho FEFO</span>
                </div>
            </div>

            {{-- Liên kết --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-green-300">Liên kết</h3>
                <ul class="space-y-2.5 text-sm text-green-100">
                    <li><a class="hover:text-white hover:underline" href="/">Trang chủ</a></li>
                    <li><a class="hover:text-white hover:underline" href="{{ Route::has('products.index') ? route('products.index') : '#' }}">Sản phẩm</a></li>
                    <li><a class="hover:text-white hover:underline" href="{{ Route::has('subscriptions.index') ? route('subscriptions.index') : '#' }}">Gói định kỳ</a></li>
                    <li><a class="hover:text-white hover:underline" href="{{ route('about') }}">Về chúng tôi</a></li>
                </ul>
            </div>

            {{-- Liên hệ --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-green-300">Liên hệ</h3>
                <ul class="space-y-3 text-sm text-green-100">
                    <li class="flex items-start gap-2"><x-icon name="mail" class="mt-0.5 h-5 w-5 shrink-0 text-green-300" /> contact@nongsansach.vn</li>
                    <li class="flex items-start gap-2"><x-icon name="phone" class="mt-0.5 h-5 w-5 shrink-0 text-green-300" /> 0123 456 789</li>
                    <li class="flex items-start gap-2"><x-icon name="pin" class="mt-0.5 h-5 w-5 shrink-0 text-green-300" /> Cao Thắng, TP. Hồ Chí Minh</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-green-800 bg-green-950">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-1 px-4 py-4 text-xs text-green-300 sm:flex-row">
            <span>© {{ date('Y') }} Nông Sản Sạch. Đồ án 11 - Nhóm 10.</span>
            <span>Cao đẳng Kỹ thuật Cao Thắng</span>
        </div>
    </div>
</footer>

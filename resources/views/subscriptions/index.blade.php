<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gói Nông Sản Định Kỳ 1 Tháng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-green-700">Gói Nông Sản Định Kỳ (1 Tháng - 4 Kỳ Giao)</h1>
                <p class="text-sm text-gray-500">Đăng ký nhận nông sản sạch tươi mới hàng tuần, tự động xuất kho theo lô FEFO.</p>
            </div>
            <a href="/" class="text-sm text-green-700 hover:underline font-medium">&larr; Về trang chủ</a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-100 border border-green-300 text-green-800 font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-300 text-red-800">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form đăng ký gói 1 tháng -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">1. Đăng ký gói 1 tháng</h2>
                <form action="{{ route('subscriptions.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">Tên gói định kỳ</label>
                        <input type="text" name="ten_goi" value="Gói Rau Củ Sạch Hàng Tuần (1 Tháng)" required
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-green-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Chọn nông sản giao mỗi kỳ</label>
                        <select name="product_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }} - {{ number_format($product->discount_price ?? $product->base_price) }}đ/kg
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Số lượng / kỳ (kg)</label>
                            <input type="number" step="0.5" min="0.5" name="so_luong" value="2" required
                                   class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Ngày giao đầu tiên</label>
                            <input type="date" name="ngay_bat_dau" value="{{ now()->addDay()->toDateString() }}" required
                                   class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Người nhận</label>
                        <input type="text" name="nguoi_nhan" value="Lê Nhân" required
                               class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Số điện thoại nhận</label>
                        <input type="text" name="sdt_nhan" value="0901234567" required
                               class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Địa chỉ giao hàng</label>
                        <textarea name="dia_chi_giao" rows="2" required
                                  class="w-full border rounded-lg px-3 py-2 text-sm">65 Huỳnh Thúc Kháng, Quận 1, TP.HCM</textarea>
                    </div>

                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                        Thanh toán mô phỏng gói 1 tháng (4 kỳ giao, mỗi tuần 1 kỳ). Hệ thống tự động sinh đơn con trước ngày giao 01 ngày.
                    </div>

                    <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-lg transition">
                        Thanh toán mô phỏng & Đăng ký gói
                    </button>
                </form>
            </div>

            <!-- Bảng danh sách gói & Lịch giao hàng -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">2. Danh sách gói đã đăng ký & Bảng lịch giao</h2>

                    @forelse($subscriptions as $sub)
                        <div class="mb-6 border rounded-lg p-4 bg-gray-50">
                            <div class="flex flex-wrap justify-between items-start gap-2 mb-3">
                                <div>
                                    <span class="font-bold text-green-700">#{{ $sub->id }} - {{ $sub->ten_goi }}</span>
                                    <p class="text-xs text-gray-600 mt-0.5">
                                        Người nhận: {{ $sub->nguoi_nhan }} ({{ $sub->sdt_nhan }}) | Địa chỉ: {{ $sub->dia_chi_giao }}
                                    </p>
                                    <p class="text-xs text-gray-600 mt-0.5">
                                        Sản phẩm:
                                        @foreach($sub->items as $item)
                                            <span class="font-semibold text-gray-800">{{ $item->product->name ?? 'SP #'.$item->product_id }} ({{ $item->so_luong }} kg/kỳ)</span>
                                        @endforeach
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $sub->trang_thai }}
                                    </span>
                                    <p class="text-sm font-bold text-gray-900 mt-1">Tổng gói: {{ number_format($sub->tong_tien) }}đ</p>
                                </div>
                            </div>

                            <div class="overflow-x-auto bg-white rounded border">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-100 text-gray-700 text-xs uppercase">
                                        <tr>
                                            <th class="px-3 py-2">Kỳ</th>
                                            <th class="px-3 py-2">Ngày giao dự kiến</th>
                                            <th class="px-3 py-2">Trạng thái sinh đơn</th>
                                            <th class="px-3 py-2">Mã đơn con</th>
                                            <th class="px-3 py-2">Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($sub->schedules as $idx => $sch)
                                            <tr>
                                                <td class="px-3 py-2 font-medium">Kỳ {{ $idx + 1 }}</td>
                                                <td class="px-3 py-2">{{ $sch->ngay_giao_du_kien->format('d/m/Y') }}</td>
                                                <td class="px-3 py-2">
                                                    @if($sch->trang_thai_sinh_don === 'DaTaoDon')
                                                        <span class="px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-800 font-semibold">DaTaoDon</span>
                                                    @elseif($sch->trang_thai_sinh_don === 'ThieuHang')
                                                        <span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-800 font-semibold">ThieuHang</span>
                                                    @else
                                                        <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-700">{{ $sch->trang_thai_sinh_don }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 font-mono text-xs text-green-700 font-semibold">
                                                    {{ $sch->order->ma_don_hang ?? '—' }}
                                                </td>
                                                <td class="px-3 py-2 text-xs text-gray-500">{{ $sch->ghi_chu }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Chưa có gói định kỳ nào. Hãy đăng ký ở biểu mẫu bên trái.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>
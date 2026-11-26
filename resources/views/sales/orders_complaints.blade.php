<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng & Khiếu nại - Sales</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .alert-success { color: green; margin-bottom: 10px; }
        .alert-error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Danh sách đơn hàng & Khiếu nại (Sales)</h2>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Trạng thái đơn</th>
                <th>Khiếu nại</th>
                <th>Xử lý (bắt buộc nhập lý do)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->ma_don_hang ?? $order->id }}</td>
                    <td>{{ $order->user->full_name ?? $order->user->email ?? 'N/A' }}</td>
                    <td>{{ $order->trang_thai }}</td>
                    <td>
                        @if($order->complaint)
                            <div><strong>Lý do KH:</strong> {{ $order->complaint->ly_do }}</div>
                            <div><strong>Trạng thái:</strong> {{ $order->complaint->trang_thai }}</div>
                            @if($order->complaint->ly_do_xu_ly)
                                <div><strong>Lý do xử lý:</strong> {{ $order->complaint->ly_do_xu_ly }}</div>
                            @endif
                        @else
                            Chưa có khiếu nại
                        @endif
                    </td>
                    <td>
                        @if($order->complaint)
                            <form action="{{ route('sales.complaints.resolve', $order->complaint->id) }}" method="POST">
                                @csrf
                                <select name="trang_thai" required>
                                    <option value="HoanTien">HoanTien</option>
                                    <option value="GiaoBu">GiaoBu</option>
                                    <option value="TuChoi">TuChoi</option>
                                </select>
                                <input type="text" name="ly_do_xu_ly" placeholder="Nhập lý do xử lý..." required>
                                <button type="submit">Lưu duyệt</button>
                            </form>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
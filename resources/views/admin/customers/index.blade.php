@extends('admin.layout')
@section('title', 'Khách hàng và điểm')
@section('content')
<h1 class="text-2xl font-bold mb-1">Khách hàng và điểm tích lũy</h1>
<p class="text-sm text-slate-500 mb-4">Quy tắc: mỗi {{ number_format($vndPerPoint, 0, ',', '.') }}đ tiền hàng (không gồm phí ship) được 1 điểm, cộng khi đơn chuyển sang Đã giao. Đơn giao bù không được cộng điểm.</p>
<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-stone-100 text-left text-slate-600">
            <tr><th class="px-4 py-2">Họ tên</th><th class="px-4 py-2">Email</th><th class="px-4 py-2">Điện thoại</th><th class="px-4 py-2 text-right">Số đơn</th><th class="px-4 py-2 text-right">Điểm</th></tr>
        </thead>
        <tbody>
        @forelse ($customers as $c)
            <tr class="border-t border-stone-100">
                <td class="px-4 py-2 font-semibold">{{ $c->full_name }}</td>
                <td class="px-4 py-2">{{ $c->email }}</td>
                <td class="px-4 py-2">{{ $c->phone }}</td>
                <td class="px-4 py-2 text-right">{{ $c->orders_count }}</td>
                <td class="px-4 py-2 text-right font-bold text-emerald-700">{{ number_format($c->loyalty_points) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Chưa có khách hàng.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $customers->links() }}</div>
@endsection

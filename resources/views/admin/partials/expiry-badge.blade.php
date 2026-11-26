@php
    $days = $batch->daysToExpiry();
    $qty = (float) $batch->current_quantity;
    if ($qty <= 0) {
        [$cls, $text] = ['bg-slate-100 text-slate-600', 'Hết hàng'];
    } elseif ($days < 0) {
        [$cls, $text] = ['bg-red-100 text-red-800', 'Đã quá hạn ' . abs($days) . ' ngày'];
    } elseif ($days <= ($alertDays ?? 2)) {
        [$cls, $text] = ['bg-red-100 text-red-800', 'Còn ' . $days . ' ngày'];
    } elseif ($days <= 5) {
        [$cls, $text] = ['bg-amber-100 text-amber-800', 'Còn ' . $days . ' ngày'];
    } else {
        [$cls, $text] = ['bg-emerald-100 text-emerald-800', 'Còn ' . $days . ' ngày'];
    }
@endphp
<span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $cls }}">{{ $text }}</span>

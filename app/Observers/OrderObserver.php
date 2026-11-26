<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\LoyaltyService;

class OrderObserver
{
    /**
     * Khi đơn chuyển sang DaGiao lần đầu thì cộng điểm cho khách (chỉ cộng 1 lần).
     */
    public function updated(Order $order): void
    {
        if (
            $order->wasChanged('trang_thai')
            && $order->trang_thai === 'DaGiao'
            && $order->getOriginal('trang_thai') !== 'DaGiao'
        ) {
            app(LoyaltyService::class)->awardForDeliveredOrder($order);
        }
    }
}

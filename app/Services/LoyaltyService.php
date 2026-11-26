<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SystemConfig;
use App\Models\User;

/**
 * Tích điểm thành viên.
 * Quy tắc: mỗi 10.000đ tiền hàng (tạm tính, không gồm phí ship) được 1 điểm.
 * Đơn giao bù (GiaoBu) không được cộng điểm.
 * Có thể đổi quy tắc bằng khóa loyalty_vnd_per_point trong bảng system_configs.
 */
class LoyaltyService
{
    public function pointsFor(Order $order): int
    {
        if ($order->loai_don === 'GiaoBu') {
            return 0;
        }

        $vndPerPoint = max(1, (int) SystemConfig::getValue('loyalty_vnd_per_point', 10000));

        return intdiv((int) round((float) $order->tam_tinh), $vndPerPoint);
    }

    public function awardForDeliveredOrder(Order $order): int
    {
        $points = $this->pointsFor($order);

        if ($points > 0) {
            User::whereKey($order->user_id)->increment('loyalty_points', $points);
        }

        return $points;
    }
}

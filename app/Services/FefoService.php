<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FefoService
{
    /**
     * Xuất số lượng một sản phẩm cho đơn hàng đã tồn tại theo FEFO.
     *
     * @return OrderDetail[] Các dòng chi tiết được tạo theo từng lô.
     */
    public function exportForOrder(
        Order $order,
        int $productId,
        float $quantity,
        int $userId
    ): array {
        if ($quantity <= 0) {
            throw new RuntimeException('Số lượng cần xuất phải lớn hơn 0.');
        }

        return DB::transaction(function () use ($order, $productId, $quantity, $userId) {
            $product = Product::query()->findOrFail($productId);

            // Giá được tính ở backend, không nhận giá từ trình duyệt.
            $unitPrice = $product->discount_price ?? $product->base_price;

            // Lấy các lô còn hàng, chưa hết hạn; khóa để tránh hai đơn
            // cùng trừ một lượng tồn kho tại cùng thời điểm.
            $batches = Batch::query()
                ->where('product_id', $productId)
                ->where('current_quantity', '>', 0)
                ->whereDate('expiry_date', '>', now()->toDateString())
                ->orderBy('expiry_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $available = (float) $batches->sum('current_quantity');

            if ($available < $quantity) {
                throw new RuntimeException(
                    "Không đủ tồn kho. Cần {$quantity}, hiện còn {$available}."
                );
            }

            $remaining = round($quantity, 2);
            $createdDetails = [];

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $batchBalance = (float) $batch->current_quantity;
                $take = min($batchBalance, $remaining);
                $take = round($take, 2);

                if ($take <= 0) {
                    continue;
                }

                $newBalance = round($batchBalance - $take, 2);

                $batch->current_quantity = $newBalance;
                if ($newBalance == 0.0) {
                    $batch->status = 'empty';
                }
                $batch->save();

                $lineTotal = round($take * (float) $unitPrice, 2);

                $createdDetails[] = OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'batch_id' => $batch->id,
                    'so_luong' => $take,
                    'don_gia' => $unitPrice,
                    'thanh_tien' => $lineTotal,
                ]);

                InventoryLog::create([
                    'batch_id' => $batch->id,
                    'user_id' => $userId,
                    'order_id' => $order->id,
                    'action_type' => 'EXPORT_FEFO',
                    'quantity_change' => -$take,
                    'balance_after' => $newBalance,
                    'reason' => 'Xuất kho theo FEFO cho đơn hàng ' . $order->ma_don_hang,
                ]);

                $remaining = round($remaining - $take, 2);
            }

            return $createdDetails;
        });
    }
}
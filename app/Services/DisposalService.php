<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\InventoryLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DisposalService
{
    public function dispose(
        array $data,
        ?UploadedFile $image,
        int $userId
    ): InventoryLog {
        return DB::transaction(function () use ($data, $image, $userId) {

            $batch = Batch::lockForUpdate()
                ->findOrFail($data['batch_id']);

            $quantity = (float) $data['quantity'];
            $currentQuantity = (float) $batch->current_quantity;

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Số lượng hủy phải lớn hơn 0.',
                ]);
            }

            if ($quantity > $currentQuantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Số lượng hủy vượt quá tồn kho hiện tại (' .
                        $currentQuantity . ').',
                ]);
            }

            $newBalance = round($currentQuantity - $quantity, 2);

            $batch->current_quantity = $newBalance;

            if ($newBalance <= 0) {
                $batch->current_quantity = 0;
                $batch->status = 'empty';
            }

            $batch->save();

            $imagePath = $image
                ? $image->store('disposals', 'public')
                : null;

            return InventoryLog::create([
                'batch_id' => $batch->id,
                'user_id' => $userId,
                'order_id' => null,
                'action_type' => 'DISPOSAL',
                'quantity_change' => -$quantity,
                'balance_after' => $newBalance,
                'reason' => $data['reason'],
                'evidence_image' => $imagePath,
            ]);
        });
    }
}
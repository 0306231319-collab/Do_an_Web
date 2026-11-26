<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\BatchDisposal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DisposalService
{
    public function dispose(array $data, ?UploadedFile $image, int $userId): BatchDisposal
    {
        return DB::transaction(function () use ($data, $image, $userId) {
            $batch = Batch::lockForUpdate()->findOrFail($data['batch_id']);

            if ($data['quantity'] > $batch->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Số lượng vượt quá tồn kho của lô (' . $batch->quantity . ').',
                ]);
            }

            $batch->decrement('quantity', $data['quantity']);

            return BatchDisposal::create([
                'batch_id'   => $batch->id,
                'user_id'    => $userId,
                'quantity'   => $data['quantity'],
                'reason'     => $data['reason'],
                'image_path' => $image?->store('disposals', 'public'),
            ]);
        });
    }
}
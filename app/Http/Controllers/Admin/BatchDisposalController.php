<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisposeBatchRequest;
use App\Models\Batch;
use App\Services\DisposalService;


class BatchDisposalController extends Controller
{
    public function create()
    {
        $batches = Batch::where('current_quantity', '>', 0)
            ->with('product')
            ->orderBy('expiry_date')
            ->get();

        return view('admin.disposals.create', compact('batches'));
    }

    public function store(
        DisposeBatchRequest $request,
        DisposalService $service
    ) {
        $service->dispose(
            $request->validated(),
            $request->file('image'),
            auth()->id()
        );

        return redirect()
            ->route('admin.batches.index')
            ->with('success', 'Đã xuất hủy thành công.');
    }
}
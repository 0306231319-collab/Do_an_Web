<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisposeBatchRequest;
use App\Models\Batch;
use App\Services\DisposalService;

class BatchDisposalController extends Controller
{
    public function create()
    {
        $batches = Batch::where('quantity', '>', 0)->orderBy('expiry_date')->get();
        return view('disposals.create', compact('batches'));
    }

    public function store(DisposeBatchRequest $request, DisposalService $service)
    {
        $service->dispose($request->validated(), $request->file('image'), auth()->id());
        return redirect()->route('batches.index')->with('success', 'Đã xuất hủy thành công.');
    }
}
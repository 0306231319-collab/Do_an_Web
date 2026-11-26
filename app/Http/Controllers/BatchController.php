<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBatchRequest;
use App\Models\Batch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with('product')->orderBy('expiry_date')->paginate(15);
        $warningDays = Batch::warningDays();
        return view('batches.index', compact('batches', 'warningDays'));
    }

    public function create()
    {
        return view('batches.form', ['batch' => new Batch(), 'products' => Product::all()]);
    }

    public function store(StoreBatchRequest $request)
    {
        Batch::create($request->validated());
        return redirect()->route('batches.index')->with('success', 'Đã thêm lô hàng.');
    }

    public function edit(Batch $batch)
    {
        return view('batches.form', ['batch' => $batch, 'products' => Product::all()]);
    }

    public function update(StoreBatchRequest $request, Batch $batch)
    {
        $batch->update($request->validated());
        return redirect()->route('batches.index')->with('success', 'Đã cập nhật lô hàng.');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();
        return redirect()->route('batches.index')->with('success', 'Đã xóa lô hàng.');
    }

    public function updateWarning(Request $request)
    {
        $data = $request->validate(['days' => 'required|integer|min:1|max:60']);
        DB::table('system_configs')->updateOrInsert(
            ['key' => 'expiry_warning_days'],
            ['value' => (string) $data['days']]
        );
        return back()->with('success', 'Đã cập nhật ngưỡng cảnh báo.');
    }
}
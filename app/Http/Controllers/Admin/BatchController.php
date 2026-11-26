<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\SystemConfig;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $alertDays = (int) SystemConfig::getValue('near_expiry_alert_days', 2);
        $today = now()->toDateString();
        $alertDate = now()->addDays($alertDays)->toDateString();

        // FEFO: hạn sử dụng gần nhất xếp trước
        $query = Batch::with('product')->orderBy('expiry_date')->orderBy('id');

        $keyword = trim((string) $request->input('q'));
        if ($keyword !== '') {
            $query->where(function ($w) use ($keyword) {
                $w->where('batch_code', 'like', "%{$keyword}%")
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$keyword}%"));
            });
        }

        $filter = (string) $request->input('filter');
        switch ($filter) {
            case 'selling':
                $query->where('current_quantity', '>', 0)->whereDate('expiry_date', '>=', $today);
                break;
            case 'near':
                $query->where('current_quantity', '>', 0)
                    ->whereDate('expiry_date', '>=', $today)
                    ->whereDate('expiry_date', '<=', $alertDate);
                break;
            case 'expired':
                $query->where('current_quantity', '>', 0)->whereDate('expiry_date', '<', $today);
                break;
            case 'empty':
                $query->where('current_quantity', '<=', 0);
                break;
        }

        $batches = $query->paginate(15)->withQueryString();

        return view('admin.batches.index', compact('batches', 'alertDays', 'keyword', 'filter'));
    }

    public function show(Batch $batch)
    {
        $alertDays = (int) SystemConfig::getValue('near_expiry_alert_days', 2);
        $batch->load(['product', 'creator']);
        $logs = $batch->inventoryLogs()->with(['user', 'order'])->orderByDesc('id')->get();
        $farmingLogs = $batch->farmingLogs()->get();

        return view('admin.batches.show', compact('batch', 'logs', 'farmingLogs', 'alertDays'));
    }
}

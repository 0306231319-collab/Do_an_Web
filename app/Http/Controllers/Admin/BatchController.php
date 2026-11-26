<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Product;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BatchController extends Controller
{
    /**
     * Danh sách lô hàng.
     */
    public function index(Request $request)
    {
        $alertDays = (int) SystemConfig::getValue(
            'near_expiry_alert_days',
            2
        );

        $today = now()->toDateString();
        $alertDate = now()->addDays($alertDays)->toDateString();

        // FEFO: hạn sử dụng gần nhất xếp trước
        $query = Batch::with('product')
            ->orderBy('expiry_date')
            ->orderBy('id');

        $keyword = trim((string) $request->input('q'));

        if ($keyword !== '') {
            $query->where(function ($w) use ($keyword) {
                $w->where('batch_code', 'like', "%{$keyword}%")
                    ->orWhereHas('product', function ($p) use ($keyword) {
                        $p->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $filter = (string) $request->input('filter');

        switch ($filter) {
            case 'selling':
                $query->where('current_quantity', '>', 0)
                    ->whereDate('expiry_date', '>=', $today);
                break;

            case 'near':
                $query->where('current_quantity', '>', 0)
                    ->whereDate('expiry_date', '>=', $today)
                    ->whereDate('expiry_date', '<=', $alertDate);
                break;

            case 'expired':
                $query->where('current_quantity', '>', 0)
                    ->whereDate('expiry_date', '<', $today);
                break;

            case 'empty':
                $query->where('current_quantity', '<=', 0);
                break;
        }

        $batches = $query->paginate(15)->withQueryString();

        return view('admin.batches.index', compact(
            'batches',
            'alertDays',
            'keyword',
            'filter'
        ));
    }

    /**
     * Form tạo lô hàng.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('admin.batches.form', [
            'batch' => new Batch(),
            'products' => $products,
            'isEdit' => false,
        ]);
    }

    /**
     * Lưu lô hàng mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'batch_code' => [
                'required',
                'string',
                'max:50',
                'unique:batches,batch_code',
            ],

            'harvest_date' => [
                'required',
                'date',
            ],

            'expiry_date' => [
                'required',
                'date',
                'after_or_equal:harvest_date',
            ],

            'initial_quantity' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'available',
                    'empty',
                    'near_expiry',
                    'expired',
                ]),
            ],
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'batch_code.required' => 'Vui lòng nhập mã lô.',
            'batch_code.unique' => 'Mã lô đã tồn tại.',
            'harvest_date.required' => 'Vui lòng chọn ngày thu hoạch.',
            'expiry_date.required' => 'Vui lòng chọn hạn sử dụng.',
            'expiry_date.after_or_equal' => 'Hạn sử dụng phải sau ngày thu hoạch.',
            'initial_quantity.required' => 'Vui lòng nhập số lượng ban đầu.',
            'initial_quantity.gt' => 'Số lượng phải lớn hơn 0.',
        ]);

        DB::transaction(function () use ($validated) {

            $quantity = $validated['initial_quantity'];

            Batch::create([
                'product_id' => $validated['product_id'],
                'created_by' => auth()->id(),
                'batch_code' => $validated['batch_code'],

                // QR URL mặc định trỏ tới mã lô
                'qr_code_url' => url('/batches/' . $validated['batch_code']),

                'harvest_date' => $validated['harvest_date'],
                'expiry_date' => $validated['expiry_date'],
                'initial_quantity' => $quantity,
                'current_quantity' => $quantity,

                'status' => $validated['status'] ?? 'available',
            ]);
        });

        return redirect()
            ->route('admin.batches.index')
            ->with('success', 'Thêm lô hàng thành công.');
    }

    /**
     * Form chỉnh sửa lô.
     */
    public function edit(Batch $batch)
    {
        $products = Product::orderBy('name')->get();

        return view('admin.batches.form', [
            'batch' => $batch,
            'products' => $products,
            'isEdit' => true,
        ]);
    }

    /**
     * Cập nhật lô hàng.
     */
    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'batch_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('batches', 'batch_code')->ignore($batch->id),
            ],

            'harvest_date' => [
                'required',
                'date',
            ],

            'expiry_date' => [
                'required',
                'date',
                'after_or_equal:harvest_date',
            ],

            'initial_quantity' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999',
            ],

            'current_quantity' => [
                'required',
                'numeric',
                'min:0',
                'lte:initial_quantity',
            ],

            'status' => [
                'required',
                Rule::in([
                    'available',
                    'empty',
                    'near_expiry',
                    'expired',
                ]),
            ],
        ]);

        DB::transaction(function () use ($validated, $batch) {

            $batch->update([
                'product_id' => $validated['product_id'],
                'batch_code' => $validated['batch_code'],
                'qr_code_url' => url('/batches/' . $validated['batch_code']),
                'harvest_date' => $validated['harvest_date'],
                'expiry_date' => $validated['expiry_date'],
                'initial_quantity' => $validated['initial_quantity'],
                'current_quantity' => $validated['current_quantity'],
                'status' => $validated['status'],
            ]);
        });

        return redirect()
            ->route('admin.batches.show', $batch)
            ->with('success', 'Cập nhật lô hàng thành công.');
    }

    /**
     * Xóa lô hàng.
     */
    public function destroy(Batch $batch)
    {
        // Không xóa lô đã phát sinh lịch sử kho hoặc đơn hàng
        if (
            $batch->inventoryLogs()->exists()
            || $batch->orderDetails()->exists()
            || $batch->farmingLogs()->exists()
        ) {
            return redirect()
                ->route('admin.batches.index')
                ->with('error', 'Không thể xóa lô đã có lịch sử kho, đơn hàng hoặc nhật ký canh tác.');
        }

        $batch->delete();

        return redirect()
            ->route('admin.batches.index')
            ->with('success', 'Xóa lô hàng thành công.');
    }

    /**
     * Cập nhật số ngày cảnh báo cận date.
     */
    public function updateAlertDays(Request $request)
    {
        $validated = $request->validate([
            'near_expiry_alert_days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],
        ], [
            'near_expiry_alert_days.required' => 'Vui lòng nhập số ngày cảnh báo.',
            'near_expiry_alert_days.integer' => 'Số ngày phải là số nguyên.',
            'near_expiry_alert_days.min' => 'Số ngày phải ít nhất là 1.',
            'near_expiry_alert_days.max' => 'Số ngày không được vượt quá 365.',
        ]);

        SystemConfig::updateOrCreate(
        ['config_key' => 'near_expiry_alert_days'],
            [
                'config_value' => $validated['near_expiry_alert_days'],
                'description' => 'Số ngày cảnh báo lô hàng sắp hết hạn.',
                'updated_by' => auth()->id(),
            ]
        );

        return redirect()
            ->route('admin.batches.index')
            ->with('success', 'Cập nhật ngưỡng cảnh báo thành công.');
    }

    /**
     * Chi tiết lô hàng.
     */
    public function show(Batch $batch)
    {
        $alertDays = (int) SystemConfig::getValue(
            'near_expiry_alert_days',
            2
        );

        $batch->load(['product', 'creator']);

        $logs = $batch->inventoryLogs()
            ->with(['user', 'order'])
            ->orderByDesc('id')
            ->get();

        $farmingLogs = $batch->farmingLogs()->get();

        return view('admin.batches.show', compact(
            'batch',
            'logs',
            'farmingLogs',
            'alertDays'
        ));
    }
}
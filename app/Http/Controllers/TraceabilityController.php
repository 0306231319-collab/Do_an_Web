<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\FarmingLog;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;
use Exception;

class TraceabilityController extends Controller
{
    /**
     * Tuần 5: Sinh mã QR cho lô hàng đã thu hoạch.
     * Quy tắc: Chỉ sinh khi đã có harvest_date; nếu đã có file QR thì không sinh mới.
     */
    public function generateQr(Batch $batch)
    {
        if (empty($batch->harvest_date)) {
            return back()->with('error', 'Chỉ được sinh mã QR khi lô hàng đã thu hoạch.');
        }

       // Đường dẫn chuẩn lưu ảnh QR SVG trong disk public (không cần imagick)
        $fileName = 'qrcodes/batch_' . $batch->batch_code . '.svg';

        // Nếu đã có file QR thực tế thì không sinh đè mã mới
        if (Storage::disk('public')->exists($fileName)) {
            return back()->with('success', 'Lô hàng này đã có mã QR sẵn.');
        }

        try {
            // Link công khai trỏ đến trang truy xuất nguồn gốc của lô
            $traceUrl = route('traceability.show', ['batch_code' => $batch->batch_code]);

            // Tạo ảnh QR SVG kích thước 300x300
            $qrSvg = QrCode::size(300)
                ->margin(1)
                ->generate($traceUrl);

            Storage::disk('public')->put($fileName, $qrSvg);

            // Lưu URL truy cập ảnh QR vào cột qr_code_url của bảng batches
            $batch->update([
                'qr_code_url' => '/storage/' . $fileName,
            ]);

            // Lưu URL truy cập ảnh QR vào cột qr_code_url của bảng batches
            $batch->update([
                'qr_code_url' => '/storage/' . $fileName,
            ]);

            return back()->with('success', 'Đã tạo mã QR thành công cho lô ' . $batch->batch_code);
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi tạo mã QR: ' . $e->getMessage());
        }
    }

    /**
     * Tuần 5: Trang truy xuất nguồn gốc công khai khi quét mã QR.
     */
    public function show(string $batch_code)
    {
        $batch = Batch::with([
            'product.category',
            'creator',
            'farmingLogs' => function ($q) {
                $q->with('staff')->orderBy('thoi_gian', 'desc');
            }
        ])->where('batch_code', $batch_code)->firstOrFail();

        return view('pages.traceability', compact('batch'));
    }

    /**
     * Tuần 5: Farm Staff thêm nhật ký canh tác (ảnh <= 5MB, video MP4 <= 20MB lên Cloudinary).
     */
    public function storeLog(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'hanh_dong'      => 'required|string|max:100',
            'thoi_gian'      => ['required', 'date', 'before_or_equal:now'],
            'mo_ta_chi_tiet' => 'nullable|string|max:2000',
            'media_file'     => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:20480', // tối đa 20MB
        ], [
            'hanh_dong.required'        => 'Vui lòng nhập loại công việc canh tác.',
            'thoi_gian.before_or_equal' => 'Ngày thực hiện không được nằm ở tương lai.',
            'media_file.mimes'          => 'Chỉ chấp nhận ảnh JPG, PNG hoặc video MP4.',
            'media_file.max'            => 'Dung lượng tệp vượt quá 20 MB.',
        ]);

        $imageUrl = null;
        $videoUrl = null;

        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $isVideo = strtolower($file->getClientOriginalExtension()) === 'mp4';

            // Kiểm tra riêng ảnh tối đa 5 MB theo quy tắc mục 6.2
            if (!$isVideo && $file->getSize() > 5 * 1024 * 1024) {
                return back()->withErrors(['media_file' => 'Ảnh minh chứng tối đa 5 MB.'])->withInput();
            }

            $resourceType = $isVideo ? 'video' : 'image';
            $uploaded = Cloudinary::uploadApi()->upload($file->getRealPath(), [
                'folder'        => 'nong-san-sach/farming-logs',
                'resource_type' => $resourceType,
            ]);

            if ($isVideo) {
                $videoUrl = $uploaded['secure_url'] ?? null;
            } else {
                $imageUrl = $uploaded['secure_url'] ?? null;
            }
        }

        FarmingLog::create([
            'batch_id'            => $batch->id,
            'farm_staff_id'       => auth()->id(),
            'hanh_dong'           => $validated['hanh_dong'],
            'mo_ta_chi_tiet'      => $validated['mo_ta_chi_tiet'] ?? null,
            'hinh_anh_minh_chung' => $imageUrl,
            'video_minh_chung'    => $videoUrl,
            'thoi_gian'           => Carbon::parse($validated['thoi_gian']),
        ]);

        return back()->with('success', 'Đã thêm nhật ký canh tác thành công.');
    }
}
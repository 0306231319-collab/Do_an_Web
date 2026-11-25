<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.15 - complaints (khiếu nại và đổi trả)
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique('uk_complaints_order_id')->constrained('orders')->restrictOnDelete();
            $table->enum('ly_do', ['DapNat', 'HuHong', 'Khac']);
            $table->text('mo_ta')->nullable();
            $table->string('hinh_anh_minh_chung')->nullable();
            $table->string('video_minh_chung')->nullable();
            $table->enum('trang_thai', ['ChoDuyet', 'HoanTien', 'GiaoBu', 'TuChoi'])->default('ChoDuyet');
            $table->foreignId('sales_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ly_do_tu_choi')->nullable();
            $table->foreignId('don_bu_id')->nullable()->unique('uk_complaints_don_bu_id')->constrained('orders')->nullOnDelete();
            $table->dateTime('ngay_tao')->useCurrent();
            $table->dateTime('ngay_duyet')->nullable();
            $table->dateTime('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();

            $table->index(['trang_thai', 'ngay_tao'], 'idx_complaints_trang_thai_ngay_tao');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE complaints ADD CONSTRAINT chk_complaints_bang_chung CHECK (hinh_anh_minh_chung IS NOT NULL OR video_minh_chung IS NOT NULL)');
            DB::statement("ALTER TABLE complaints ADD CONSTRAINT chk_complaints_tu_choi CHECK (trang_thai <> 'TuChoi' OR ly_do_tu_choi IS NOT NULL)");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

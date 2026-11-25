<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.14 - farming_logs (nhật ký canh tác)
        Schema::create('farming_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->restrictOnDelete();
            $table->foreignId('farm_staff_id')->constrained('users')->restrictOnDelete();
            $table->string('hanh_dong', 100);
            $table->text('mo_ta_chi_tiet')->nullable();
            $table->string('hinh_anh_minh_chung')->nullable();
            $table->string('video_minh_chung')->nullable();
            $table->dateTime('thoi_gian');
            $table->dateTime('ngay_tao')->useCurrent();

            $table->index(['batch_id', 'thoi_gian'], 'idx_farming_logs_batch_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farming_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.11 - subscriptions (gói định kỳ)
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('ten_goi', 150);
            $table->string('nguoi_nhan', 100);
            $table->string('sdt_nhan', 15);
            $table->text('dia_chi_giao');
            $table->decimal('tong_tien', 12, 2)->default(0);
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc');
            $table->enum('trang_thai', ['ChoThanhToan', 'DangHoatDong', 'HetHan', 'DaHuy'])->default('ChoThanhToan');
            $table->dateTime('ngay_tao')->useCurrent();
            $table->dateTime('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();

            $table->index(['trang_thai', 'ngay_ket_thuc'], 'idx_subscriptions_trang_thai_ngay_ket_thuc');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE subscriptions ADD CONSTRAINT chk_subscriptions_tong_tien CHECK (tong_tien >= 0)');
            DB::statement('ALTER TABLE subscriptions ADD CONSTRAINT chk_subscriptions_ngay CHECK (ngay_ket_thuc > ngay_bat_dau)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

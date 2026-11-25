<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.13 - delivery_schedules (mỗi dòng là một ngày giao cụ thể)
        Schema::create('delivery_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->date('ngay_giao_du_kien');
            $table->foreignId('order_id')->nullable()->unique('uk_delivery_schedules_order')->constrained('orders')->nullOnDelete();
            $table->enum('trang_thai_sinh_don', ['ChoTaoDon', 'DaTaoDon', 'ThieuHang', 'DaBoQua'])->default('ChoTaoDon');
            $table->foreignId('nguoi_xu_ly_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ghi_chu')->nullable();
            $table->dateTime('ngay_tao')->useCurrent();
            $table->dateTime('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['subscription_id', 'ngay_giao_du_kien'], 'uk_delivery_schedules_sub_date');
            $table->index(['ngay_giao_du_kien', 'trang_thai_sinh_don'], 'idx_schedules_ngay_giao_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_schedules');
    }
};

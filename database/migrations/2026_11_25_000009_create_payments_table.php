<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.10 - payments (cho đơn hàng hoặc trả trước gói định kỳ)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->cascadeOnDelete();
            $table->enum('phuong_thuc', ['COD', 'VNPay', 'MoMo'])->default('COD');
            $table->decimal('so_tien', 12, 2);
            $table->string('ma_giao_dich', 100)->nullable()->unique();
            $table->enum('trang_thai', ['ChoThanhToan', 'ThanhCong', 'ThatBai', 'HoanTien'])->default('ChoThanhToan')->index();
            $table->dateTime('ngay_thanh_toan')->nullable();
            $table->json('du_lieu_ipn')->nullable();
        });

        // Lưu ý: MySQL không cho CHECK trên cột khóa ngoại có ON DELETE CASCADE,
        // nên quy tắc "phải có order_id hoặc subscription_id" kiểm tra ở tầng ứng dụng.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE payments ADD CONSTRAINT chk_payments_so_tien CHECK (so_tien >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

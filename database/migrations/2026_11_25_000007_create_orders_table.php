<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.8 - orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->string('ma_don_hang', 30)->unique();
            $table->enum('loai_don', ['Thuong', 'DinhKy', 'GiaoBu'])->default('Thuong');
            $table->string('nguoi_nhan', 100);
            $table->string('sdt_nhan', 15);
            $table->text('dia_chi_giao');
            $table->string('ghi_chu', 500)->nullable();
            $table->decimal('tam_tinh', 12, 2)->default(0);
            $table->decimal('phi_van_chuyen', 12, 2)->default(0);
            $table->decimal('tong_tien', 12, 2)->default(0);
            $table->enum('trang_thai', ['ChoDuyet', 'DaXacNhan', 'DangGiao', 'DaGiao', 'DaHuy'])->default('ChoDuyet');
            $table->dateTime('ngay_tao')->useCurrent();
            $table->dateTime('ngay_giao_thuc_te')->nullable();

            $table->index(['trang_thai', 'ngay_tao'], 'idx_orders_trang_thai_ngay_tao');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_tien CHECK (tam_tinh >= 0 AND phi_van_chuyen >= 0 AND tong_tien >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

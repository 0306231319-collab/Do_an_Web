<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.5 - inventory_logs (nhập kho, xuất FEFO, xuất hủy)
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->enum('action_type', ['IMPORT', 'EXPORT_FEFO', 'DISPOSAL']);
            $table->decimal('quantity_change', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->text('reason')->nullable();
            $table->string('evidence_image')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE inventory_logs ADD CONSTRAINT chk_inventory_logs_balance CHECK (balance_after >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};

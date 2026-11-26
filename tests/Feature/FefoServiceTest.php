<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Services\FefoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;


class FefoServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_exports_from_batches_in_fefo_order_and_skips_expired_or_empty_batches(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Danh mục test',
            'slug' => 'danh-muc-test',
            'description' => 'Danh mục dùng trong kiểm thử.',
            'status' => true,
        ]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'base_price' => 10000,
            'discount_price' => null,
        ]);

        $order = $this->createOrder($user);

        // Lô hết hạn: không được chọn
        $expiredBatch = $this->createBatch($product, $user, now()->subDay(), 5);

        // Lô hết hàng: không được chọn
        $emptyBatch = $this->createBatch($product, $user, now()->addDays(1), 0);

        // Lô còn hạn, hạn gần nhất: xuất 3kg trước
        $batchA = $this->createBatch($product, $user, now()->addDays(2), 3);

        // Lô còn hạn tiếp theo: xuất 7kg
        $batchB = $this->createBatch($product, $user, now()->addDays(5), 8);

        $details = app(FefoService::class)->exportForOrder(
            $order,
            $product->id,
            10,
            $user->id
        );

        $this->assertCount(2, $details);
        $this->assertSame(
            [$batchA->id, $batchB->id],
            array_map(fn ($detail) => $detail->batch_id, $details)
        );

        $this->assertEquals(0, $batchA->fresh()->current_quantity);
        $this->assertEquals(1, $batchB->fresh()->current_quantity);

        // Các lô không hợp lệ phải giữ nguyên tồn kho
        $this->assertEquals(5, $expiredBatch->fresh()->current_quantity);
        $this->assertEquals(0, $emptyBatch->fresh()->current_quantity);
    }

    public function test_it_does_not_change_stock_when_quantity_is_insufficient(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Danh mục test',
            'slug' => 'danh-muc-test',
            'description' => 'Danh mục dùng trong kiểm thử.',
            'status' => true,
        ]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $order = $this->createOrder($user);

        $batch = $this->createBatch($product, $user, now()->addDays(3), 3);

        try {
            app(FefoService::class)->exportForOrder(
                $order,
                $product->id,
                8,
                $user->id
            );

            $this->fail('Dự kiến service phải báo lỗi khi không đủ tồn kho.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Không đủ tồn kho', $exception->getMessage());
        }

        $this->assertEquals(3, $batch->fresh()->current_quantity);
        $this->assertDatabaseCount('order_details', 0);
        $this->assertDatabaseCount('inventory_logs', 0);
    }

    private function createOrder(User $user): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'ma_don_hang' => 'TEST-' . uniqid(),
            'loai_don' => 'Thuong',
            'nguoi_nhan' => 'Người nhận test',
            'sdt_nhan' => '0900000000',
            'dia_chi_giao' => 'Địa chỉ test',
            'tam_tinh' => 0,
            'phi_van_chuyen' => 0,
            'tong_tien' => 0,
            'trang_thai' => 'ChoDuyet',
        ]);
    }

    private function createBatch(
        Product $product,
        User $user,
        $expiryDate,
        float $quantity
    ): Batch {
        return Batch::factory()->create([
            'product_id' => $product->id,
            'created_by' => $user->id,
            'harvest_date' => now()->subDays(10)->toDateString(),
            'expiry_date' => $expiryDate->toDateString(),
            'initial_quantity' => $quantity,
            'current_quantity' => $quantity,
            'status' => $quantity > 0 ? 'available' : 'empty',
        ]);
    }
}

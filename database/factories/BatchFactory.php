<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Sinh lô hàng có hạn sử dụng ngẫu nhiên để thử thuật toán FEFO.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Batch>
 */
class BatchFactory extends Factory
{
    public function definition(): array
    {
        $harvest = now()->subDays(fake()->numberBetween(0, 10))->startOfDay();
        $expiry = $harvest->copy()->addDays(fake()->numberBetween(1, 14));
        $initial = fake()->numberBetween(5, 50);

        return [
            'product_id' => Product::factory(),
            'created_by' => User::query()->where('role', 'farm_staff')->value('id')
                ?? User::factory()->state(['role' => 'farm_staff']),
            'batch_code' => 'LOT-' . $harvest->format('Ymd') . '-' . fake()->unique()->numerify('9##'),
            'qr_code_url' => '/truy-xuat/' . fake()->unique()->uuid(),
            'harvest_date' => $harvest->toDateString(),
            'expiry_date' => $expiry->toDateString(),
            'initial_quantity' => $initial,
            'current_quantity' => $initial,
            'status' => 'available',
        ];
    }
}

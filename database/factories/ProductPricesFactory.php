<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductPrices>
 */
class ProductPricesFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => $this->faker->unique()->numerify('2000########'),
            'value' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 100, 5000),
            'old_price' => 0,
        ];
    }
}

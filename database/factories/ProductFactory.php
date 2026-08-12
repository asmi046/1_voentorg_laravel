<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => $this->faker->unique()->numerify('2000########'),
            'group' => $this->faker->word(),
            'title' => $this->faker->words(3, true),
            'slug' => $this->faker->unique()->slug(),
            'img' => null,
            'description' => $this->faker->sentence(),
            'short_description' => $this->faker->sentence(),
            'specification' => null,
            'weight' => 1,
            'length' => 30,
            'width' => 20,
            'height' => 10,
        ];
    }
}

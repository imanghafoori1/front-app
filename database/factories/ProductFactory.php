<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(random_int(2, 4), true),
            'description' => $this->faker->sentence(random_int(10, 15)),
            'price' => $this->faker->randomFloat(1, 100, 1000),
            'image' => 'sample-image.jpg',
        ];
    }
}

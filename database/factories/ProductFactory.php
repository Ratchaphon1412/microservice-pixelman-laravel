<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

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
            //
            'name' => fake()->name(),
            'description' => fake()->text(),
            'category_id' => Category::all()->unique()->random()->id,
            'price' => fake()->randomFloat(2, 0, 1000),
            'status' => fake()->randomElement(['active', 'inactive']),
            'gender' => fake()->randomElement(['Unisex', 'Men', 'Women', 'Kids'])
        ];
    }
}

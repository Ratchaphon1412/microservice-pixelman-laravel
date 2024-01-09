<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Size;
use App\Models\Color;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
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
            'product_id' => Product::all()->random()->id,
            'size_id' => Size::whereNotIn('id', function ($query) {
                $query->select('size_id')
                    ->from('stocks')
                    ->whereColumn('product_id', 'stocks.product_id');
            })->inRandomOrder()->first()->id,
            'color_id' => Color::all()->random()->id,
            'quantity' => fake()->randomDigit(),
            'price' => fake()->randomFloat(2, 0, 1000),

        ];
    }
}

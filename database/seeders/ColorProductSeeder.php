<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Color;
use App\Models\Stock;


class ColorProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $products = Product::all();

        foreach ($products as $product) {
            $stocks = $product->stocks;
            $temp = array();
            foreach ($stocks as $stock) {
                $color = $stock->color_id;

                // BEGIN: be15d9bcejpp
                if (!in_array($color, $temp)) {
                    array_push($temp, $color);
                }
            }
            $product->colors()->syncWithoutDetaching($temp);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Models\Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $white = Color::where('hex_color', '#ffffff')->firstOrFail()->id;
        $black = Color::where('hex_color', '#000000')->firstOrFail()->id;
        $sizeS = Size::where('name', 'S')->firstOrFail()->id;
        $sizeM = Size::where('name', 'M')->firstOrFail()->id;
        $tops = Category::where('name', 'Tops')->firstOrFail()->id;
        $bottoms = Category::where('name', 'Bottoms')->firstOrFail()->id;
        $tShirts = Category::where('name', 'T-Shirts')->firstOrFail()->id;
        $jeans = Category::where('name', 'Jeans')->firstOrFail()->id;

        $product = new Product();
        $product->name = 'T-Shirt';
        $product->description = 'T-Shirt';
        $product->category_id = $tShirts;
        $product->price = 100;
        $product->gender = 'Unisex';
        $product->save();
        $product->colors()->sync([$white, $black]);
        $product->sizes()->sync([$sizeS, $sizeM]);
        $product->categories()->sync([$tops, $tShirts]);

        $stock = new Stock();
        $stock->product_id = $product->id;
        $stock->color_id = $white;
        $stock->size_id = $sizeS;
        $stock->quantity = 10;
        $stock->save();
    }
}

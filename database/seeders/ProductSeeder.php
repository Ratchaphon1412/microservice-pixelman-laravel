<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tops = Category::where('name', 'Tops')->value('id');
        $bottoms = Category::where('name', 'Bottoms')->value('id');
        $tShirts = Category::where('name', 'T-Shirts')->value('id');
        $jeans = Category::where('name', 'Jeans')->value('id');
        $dress = Category::where('name', 'Dress')->value('id');

        $colors = Color::whereIn('hex_color', ['#ffffff', '#000000', '#ff0000', '#00ff00'])->pluck('id')->toArray();
        $sizes = Size::whereIn('name', ['S', 'M', 'L', 'XL'])->pluck('id')->toArray();

        $imagePaths = [
            'image1.png',
            'image2.png',
            'image3.png',
        ];

        $products = [
            [
                'name' => 'T-Shirt',
                'description' => 'T-Shirt',
                'category' => $tShirts,
                'categories' => [$tops, $tShirts],
                'price' => 100,
                'gender' => 'Unisex',
            ],
            [
                'name' => 'Jeans',
                'description' => 'Jeans',
                'category' => $jeans,
                'categories' => [$bottoms, $jeans],
                'price' => 150,
                'gender' => 'Men',
            ],
            [
                'name' => 'Dress',
                'description' => 'Dress',
                'category' => $dress,
                'categories' => [$tops, $dress],
                'price' => 200,
                'gender' => 'Women',
            ],
        ];

        foreach ($products as $key => $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'category_id' => $productData['category'],
                'price' => $productData['price'],
                'gender' => $productData['gender'],
            ]);

            $image = Image::create([
                'path' => $imagePaths[$key],
                'product_id' => $product->id,
            ]);

            // $numSizes = rand(1, count($sizes));
            // $selectedSizes = (array) array_rand($sizes, $numSizes);
            $product->sizes()->sync($sizes);

            // $numColors = rand(1, count($colors));
            // $selectedColors = (array) array_rand($colors, $numColors);
            $product->colors()->sync($colors);

            $product->categories()->attach($productData['categories']);
            $product->images()->save($image);

            foreach ($sizes as $sizeId) {
                foreach ($colors as $colorId) {
                    Stock::create([
                        'product_id' => $product->id,
                        'color_id' => $colorId,
                        'size_id' => $sizeId,
                        'quantity' => rand(20, 40),
                    ]);
                }
            }
        }
    }
}

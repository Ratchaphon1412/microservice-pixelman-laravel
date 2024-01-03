<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Stock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;

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

        $products = [
            [
                'name' => 'T-Shirt',
                'description' => 'T-Shirt',
                'category' => $tShirts,
                'price' => 100,
                'gender' => 'Unisex',
                'images' => ['image1.png', 'image2.png', 'image3.png'],
            ],
            [
                'name' => 'Jeans',
                'description' => 'Jeans',
                'category' => $jeans,
                'price' => 150,
                'gender' => 'Men',
                'images' => ['image4.png', 'image5.png', 'image6.png'],
            ],
            [
                'name' => 'Dress',
                'description' => 'Dress',
                'category' => $dress,
                'price' => 200,
                'gender' => 'Women',
                'images' => ['image7.png', 'image8.png', 'image9.png'],
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'category_id' => $productData['category'],
                'price' => $productData['price'],
                'gender' => $productData['gender'],
            ]);

            foreach ($productData['images'] as $imagePath) {
                $image = Image::create([
                    'path' => $imagePath,
                    'product_id' => $product->id,
                ]);

                $product->images()->save($image);
            }

            $product->sizes()->sync($sizes);
            $product->colors()->sync($colors);
            $product->category()->associate($productData['category']);

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

        Redis::flushAll();
        // $productsKey = 'products';
        // $products = Product::all();
        // Redis::set($productsKey, json_encode($products));
    }
}

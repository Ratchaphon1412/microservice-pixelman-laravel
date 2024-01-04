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
                'stocks' => [
                    [
                        'color_id' => $colors[0],
                        'sizes' => [
                            ['size_id' => $sizes[0], 'quantity' => 30, 'price' => 50],
                            ['size_id' => $sizes[1], 'quantity' => 20, 'price' => 55],
                        ],
                    ],
                    [
                        'color_id' => $colors[1],
                        'sizes' => [
                            ['size_id' => $sizes[2], 'quantity' => 25, 'price' => 60],
                            ['size_id' => $sizes[3], 'quantity' => 18, 'price' => 65],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Jeans',
                'description' => 'Jeans',
                'category' => $jeans,
                'price' => 150,
                'gender' => 'Men',
                'images' => ['image4.png', 'image5.png', 'image6.png'],
                'stocks' => [
                    [
                        'color_id' => $colors[2],
                        'sizes' => [
                            ['size_id' => $sizes[0], 'quantity' => 28, 'price' => 70],
                            ['size_id' => $sizes[1], 'quantity' => 22, 'price' => 75],
                        ],
                    ],
                    [
                        'color_id' => $colors[3],
                        'sizes' => [
                            ['size_id' => $sizes[2], 'quantity' => 20, 'price' => 80],
                            ['size_id' => $sizes[3], 'quantity' => 15, 'price' => 85],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Dress',
                'description' => 'Dress',
                'category' => $dress,
                'price' => 200,
                'gender' => 'Women',
                'images' => ['image7.png', 'image8.png', 'image9.png'],
                'stocks' => [
                    [
                        'color_id' => $colors[0],
                        'sizes' => [
                            ['size_id' => $sizes[0], 'quantity' => 35, 'price' => 90],
                            ['size_id' => $sizes[1], 'quantity' => 28, 'price' => 95],
                        ],
                    ],
                    [
                        'color_id' => $colors[2],
                        'sizes' => [
                            ['size_id' => $sizes[2], 'quantity' => 18, 'price' => 100],
                            ['size_id' => $sizes[3], 'quantity' => 15, 'price' => 105],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'category_id' => $productData['category'],
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

            foreach ($productData['stocks'] as $stockData) {
                $color_id = $stockData['color_id'];

                foreach ($stockData['sizes'] as $sizeData) {
                    $size_id = $sizeData['size_id'];
                    $quantity = $sizeData['quantity'];
                    $price = $sizeData['price'];

                    Stock::create([
                        'product_id' => $product->id,
                        'color_id' => $color_id,
                        'size_id' => $size_id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);
                }
            }
        }
        Redis::flushAll();
    }
}

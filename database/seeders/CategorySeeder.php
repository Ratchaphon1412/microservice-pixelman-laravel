<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Tops', 'children' => [
                ['name' => 'Shirt'],
                ['name' => 'Jacket'],
                ['name' => 'Dress'],
                ['name' => 'Hoodie'],
                ['name' => 'T-Shirts'],
            ]],

            ['name' => 'Bottoms', 'children' => [
                ['name' => 'Pants'],
                ['name' => 'Shoes'],
                ['name' => 'Socks'],
                ['name' => 'Jeans'],
                ['name' => 'Skirt'],
                ['name' => 'Accessories'],
            ]],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create(['name' => $categoryData['name']]);

            if (isset($categoryData['children'])) {
                foreach ($categoryData['children'] as $childData) {
                    $subCategory = Category::create($childData);
                    $category->subCategories()->attach($subCategory);
                }
            }
        }
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ImageFactory extends Factory
{
    public function definition(): array
    {

        $storagePath = Storage::disk('public')->path('images');
        $imagePath = fake()->image($storagePath, 640, 480, null, false);
        // dd($imagePath, $storagePath);
        $imageContents = Storage::disk()->get('public/images' . '/' . $imagePath);
        // dd($imageContents, $imagePath);
        // Save the image to the storage cloud

        Storage::cloud()->put('images/' . $imagePath, $imageContents);

        $url = Storage::cloud()->url('test/images/' . $imagePath);

        // Get the URL path of the stored image


        return [
            'product_id' => Product::all()->random()->id,
            'path' => $url,
        ];
    }
}

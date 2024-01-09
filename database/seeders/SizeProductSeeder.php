<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Size;
use App\Models\Product;
use App\Models\Stock;

class SizeProductSeeder extends Seeder
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
                $sizeid = $stock->size_id;

                // BEGIN: be15d9bcejpp
                if (!in_array($sizeid, $temp)) {
                    array_push($temp, $sizeid);
                }
                // END: be15d9bcejpp
            }

            $product->sizes()->syncWithoutDetaching($temp);
        }
    }
}

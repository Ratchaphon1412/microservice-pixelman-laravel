<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $color = new Color();
        $color->hex_color = '#ffffff';
        $color->save();

        $color = new Color();
        $color->hex_color = '#000000';
        $color->save();

        $color = new Color();
        $color->hex_color = '#ff0000';
        $color->save();
    }
}

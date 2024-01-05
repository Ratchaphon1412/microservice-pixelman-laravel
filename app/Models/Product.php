<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;


class Product extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'price',
        'status',
        'gender'
    ];

    public function colors()
    {
        return $this->belongsToMany(Color::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // public function categories()
    // {
    //     return $this->belongsToMany(Category::class);
    // }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function formatProduct()
    {
        $stocks = $this->stocks;
        $lowestPrice = PHP_INT_MAX; // Initialize with the maximum possible integer value
        if (count($stocks) != 0) {
            foreach ($stocks as $stock) {
                if ($stock->price < $lowestPrice) {
                    $lowestPrice = $stock->price;
                }
            }
        } else {
            $lowestPrice = 0;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category->name,
            'price' => $lowestPrice,
            'status' => $this->status,
            'gender' => $this->gender,
            'images' => $this->images->map(function ($image) {
                return $image->path;
            }),
        ];
    }

    public function toSearchableArray()
    {
        $array = $this->with('colors', 'sizes', 'images')->where('id', $this->id)->first()->toArray();
        return $array;
    }
}

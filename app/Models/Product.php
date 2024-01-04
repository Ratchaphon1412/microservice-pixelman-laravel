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
    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        // Customize array...
        $array['image_product'] = $this->images()->first();
        $array['category_name'] = $this->category()->first()->name;
        $array['color_all'] = $this->colors()->get();
        $array['size_all'] = $this->sizes()->get();

        return $array;
    }
}

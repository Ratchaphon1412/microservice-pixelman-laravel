<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Redis;

class ProductCacheService
{
    /**
     * Updates the Redis cache with the latest products.
     *
     * This function retrieves all the products from the database, including their
     * associated category and images. It then formats each product using the
     * `formatProduct` method and stores the formatted products in Redis cache.
     */
    public function updateRedisProducts()
    {
        $products = Product::with('category', 'images')->get();
        $formattedProducts = $products->map(fn ($product) => $product->formatProduct());
        Redis::set('products', $formattedProducts->toJson());
    }
}

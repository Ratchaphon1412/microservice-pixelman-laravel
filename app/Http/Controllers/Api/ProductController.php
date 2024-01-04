<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private function formatProduct($product)
    {
        $stocks = $product->stocks;
        $lowestPrice = PHP_INT_MAX; // Initialize with the maximum possible integer value

        foreach ($stocks as $stock) {
            if ($stock->price < $lowestPrice) {
                $lowestPrice = $stock->price;
            }
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'category' => $product->category->name,
            'price' => $lowestPrice,
            'status' => $product->status,
            'gender' => $product->gender,
            'images' => self::formatImages($product->images),
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];
    }

    private static function formatImages($images)
    {
        return $images->map(function ($image) {
            return $image->path;
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productsKey = 'products';

        // Check if the data is already in the cache
        $cachedData = Redis::get($productsKey);

        if ($cachedData) {
            // If cached data exists, return it
            return response()->json(json_decode($cachedData, true));
        } else {
            // If not cached, fetch products from the database
            $products = Product::with('category', 'images')->get();

            // Format the fetched data
            $formattedProducts = $products->map(function ($product) {
                return $this->formatProduct($product);
            });

            // Store the fetched data in the cache
            Redis::set($productsKey, json_encode($formattedProducts));

            // Return the fetched data
            return response()->json($formattedProducts);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate User Token

        // Validate Data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'gender' => 'required|string',
            'stocks' => 'required|array',
            'stocks.*.color_id' => 'required|exists:colors,id',
            'stocks.*.sizes' => 'required|array',
            'stocks.*.sizes.*.size_id' => 'required|exists:sizes,id',
            'stocks.*.sizes.*.quantity' => 'required|integer|min:0',
            'stocks.*.sizes.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $productData = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
            'gender' => $request->input('gender'),
        ];

        $product = Product::create($productData);

        $stocksData = $request->input('stocks');

        $stocks = [];
        foreach ($stocksData as $stockData) {
            $color_id = $stockData['color_id'];

            foreach ($stockData['sizes'] as $sizeData) {
                $stock = [
                    'product_id' => $product->id,
                    'color_id' => $color_id,
                    'size_id' => $sizeData['size_id'],
                    'quantity' => $sizeData['quantity'],
                    'price' => $sizeData['price'],
                ];

                $stocks[] = $stock;
            }
        }
        Stock::insert($stocks);

        // Update products in Redis
        $products = Product::with('category', 'images')->get();

        $formattedProducts = $products->map(function ($product) {
            return $this->formatProduct($product);
        });

        $productsKey = 'products';
        Redis::set($productsKey, json_encode($formattedProducts));

        if ($request->hasFile('images')) {
            $images = $request->file('images');
            $uploadedImages = [];

            foreach ($images as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('product/images'), $imageName);
                $path = 'product/images/' . $imageName;

                $uploadedImages[] = [
                    'product_id' => $product->id,
                    'path' => $path,
                ];
            }
            Image::insert($uploadedImages);
        }


        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product = Product::find($product->id);
        return $product;
    }

    public function activate($id)
    {

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update(['status' => 'active']);

        // Update products in Redis
        Redis::set('products', json_encode(Product::all()));

        return response()->json(['message' => 'Product activated successfully'], 200);
    }

    public function deactivate($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update(['status' => 'inactive']);

        // Update products in Redis
        Redis::set('products', json_encode(Product::all()));

        return response()->json(['message' => 'Product deactivated successfully'], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}

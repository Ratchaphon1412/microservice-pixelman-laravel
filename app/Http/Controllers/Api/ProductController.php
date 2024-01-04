<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Product;
use App\Models\Stock;
use App\Services\ProductCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Input\Input;

class ProductController extends Controller
{
    private $productCacheService;

    public function __construct(ProductCacheService $productCacheService)
    {
        $this->productCacheService = $productCacheService;
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
                return $product->formatProduct($product);
            });

            // Store the fetched data in the cache
            Redis::set('products', json_encode($formattedProducts));

            // Return the fetched data
            return response()->json($formattedProducts);
        }
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
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $product = Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
            'gender' => $request->input('gender'),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/products'), $imageName);
                $path = 'images/products/' . $imageName;

                $product->images()->create([
                    'product_id' => $product->id,
                    'path' => $path,
                ]);
            }
        }

        // Update products in Redis using the service
        $this->productCacheService->updateRedisProducts();

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with('category', 'images')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return $product->formatProduct();
    }

    public function activate($id)
    {

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update(['status' => 'active']);

        // Update products in Redis using the service
        $this->productCacheService->updateRedisProducts();

        return response()->json(['message' => 'Product activated successfully'], 200);
    }

    public function deactivate($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update(['status' => 'inactive']);

        // Update products in Redis using the service
        $this->productCacheService->updateRedisProducts();

        return response()->json(['message' => 'Product deactivated successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
            'gender' => $request->input('gender'),
        ]);

        // Update products in Redis using the service
        $this->productCacheService->updateRedisProducts();

        return response()->json(['message' => 'Product updated successfully', 'product' => $product], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->stocks()->delete();
        $product->delete();

        // Update products in Redis using the service
        $this->productCacheService->updateRedisProducts();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}

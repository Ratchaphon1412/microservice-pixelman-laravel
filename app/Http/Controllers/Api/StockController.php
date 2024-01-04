<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Services\ProductCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
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
        $stocksKey = 'stocks';
        $cachedData = Redis::get($stocksKey);

        if ($cachedData) {
            return response()->json(json_decode($cachedData, true));
        } else {
            $stocks = Stock::all();
            Redis::set($stocksKey, json_encode($stocks));
            return response()->json($stocks);
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
            'stocks' => 'required|array',
            'stocks.*.color_id' => 'required',
            'stocks.*.sizes' => 'required|array',
            'stocks.*.sizes.*.size_id' => 'required',
            'stocks.*.sizes.*.quantity' => 'required|integer|min:0',
            'stocks.*.sizes.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $product_id = $request->input('product_id');
        $product = Product::find($product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $stockData = $request->input('stocks');
        $lowestPrice = PHP_INT_MAX;
        foreach ($stockData as $stockData) {
            $color_id = $stockData['color_id'];

            foreach ($stockData['sizes'] as $sizeData) {
                $sizeDataPrice = $sizeData['price'];
                $lowestPrice = min($lowestPrice, $sizeDataPrice);

                // Create stock
                Stock::create([
                    'product_id' => $product_id,
                    'color_id' => $color_id,
                    'size_id' => $sizeData['size_id'],
                    'quantity' => $sizeData['quantity'],
                    'price' => $sizeDataPrice,
                ]);
            }
        }

        Product::where('id', $product_id)->update(['price' => $lowestPrice]);


        // Update products in Redis using the service
        $this->productCacheService->updateRedisProducts();

        // Update stocks in Redis
        Redis::set('stocks', json_encode(Stock::all()));

        return response()->json(['message' => 'Stocks created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        return $stock;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $stock = Stock::find($id);
        if (!$stock) {
            return response()->json(['message' => 'Stock not found'], 404);
        }
        $quantity = $request->input('quantity');
        $price = $request->input('price');
        $stock->update([
            'quantity' => $quantity,
            'price' => $price
        ]);

        // Update products in Redis using the service
        $this->productCacheService->updateRedisProducts();

        // Update stocks in Redis
        Redis::set('stocks', json_encode(Stock::all()));

        return response()->json(['message' => 'Stock updated successfully', 'stock' => $stock], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();

        // Update products in Redis using the service
        $this->productCacheService->updateRedisProducts();

        // Update stocks in Redis
        Redis::set('stocks', json_encode(Stock::all()));

        return response()->json(['message' => 'Stock deleted successfully'], 200);
    }
}

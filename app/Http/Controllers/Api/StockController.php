<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = Stock::all();
        return $stocks;
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
            'stocks' => 'required|array',
            'stocks.*.color_id' => 'required',
            'stocks.*.sizes' => 'required|array',
            'stocks.*.sizes.*.size_id' => 'required',
            'stocks.*.sizes.*.quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product_id = $request->input('product_id');
        $stocks = $request->input('stocks');

        foreach ($stocks as $stockData) {
            $color_id = $stockData['color_id'];

            foreach ($stockData['sizes'] as $sizeData) {
                $size_id = $sizeData['size_id'];
                $quantity = $sizeData['quantity'];

                // Create stock
                Stock::create(
                    [
                        'product_id' => $product_id,
                        'color_id' => $color_id,
                        'size_id' => $size_id,
                        'quantity' => $quantity
                    ],
                );
            }
        }

        return response()->json(['message' => 'Stocks created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        //
    }
}

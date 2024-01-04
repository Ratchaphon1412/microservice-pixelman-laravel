<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Api\StockController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('products', ProductController::class);
Route::put('products/{id}/activate', [ProductController::class, 'activate']);
Route::put('products/{id}/deactivate', [ProductController::class, 'deactivate']);
// Route::put('products/{product}/deactivate', [ProductController::class, 'deactivate']);
Route::get('search', SearchController::class)->name('search');

Route::put('stocks/{id}', [StockController::class, 'update']);
Route::apiResource('stocks', StockController::class);

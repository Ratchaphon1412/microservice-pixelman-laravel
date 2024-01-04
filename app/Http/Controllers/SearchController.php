<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use MeiliSearch\Endpoints\Indexes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;

class SearchController extends Controller
{
    //
    public function __invoke(Request $request)
    {
        $product = Product::search(
            trim($request->get("query")) ?? '',
            function (Indexes $meiliSearch, string $query, array $options) use ($request) {
                // filter something

                return $meiliSearch->search($query, $options);
            }
        );

        return response()->json([
            'data' => $product->query(fn (Builder $query) => $query->with('category'))->paginate(10) ?? [],
            'status' => 200
        ]);
    }
}

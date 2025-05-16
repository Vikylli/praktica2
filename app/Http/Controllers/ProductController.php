<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function show(Product $product)
    {
        return response()->json([
            'data' => new ProductResource($product)
        ], 200);
    }
}

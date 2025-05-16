<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
      public function index(Category $category)
      {
        $categories = Category::all()
        ->select(['id', 'name', 'description']);
        return response()->json(['data' => $categories], 200);
    }

      public function products(Category $category)
      {
        $products = $category->products()
        ->select(['id', 'name', 'description', 'price', 'image_url'])
        ->get();

        return response()->json(['data' => $products], 200);
    }
}

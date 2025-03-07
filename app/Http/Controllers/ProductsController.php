<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);
        
        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        $products = $query->get();
        $categories = Category::all();
        
        return view('products.index', compact('products', 'categories'));
    }
    
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}
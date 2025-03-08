<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        
        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        $products = $query->with('images')->get();
        $categories = Category::all();
        
        // Get cart for the cart counter
        $cart = app(CartController::class)->getCart();
        
        return view('products.index', compact('products', 'categories', 'cart'));
    }
    
    public function show(Product $product)
    {
        $product->load('images', 'category', 'reviews.user');
        
        // Get related products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->take(3)
            ->get();
            
        // Get cart for the cart counter
        $cart = app(CartController::class)->getCart();
        
        return view('products.show', compact('product', 'relatedProducts', 'cart'));
    }
    
}
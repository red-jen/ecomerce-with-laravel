<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    // Get or create a cart for the current session/user
    public function getCart()
    {
        $sessionId = session()->getId();
        $userId = auth()->id();
        
        $cart = Cart::where('session_id', $sessionId)
            ->orWhere(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereNotNull('user_id');
            })
            ->first();
            
        if (!$cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
            ]);
        }
        
        // If user is logged in but cart doesn't have user_id, update it
        if ($userId && !$cart->user_id) {
            $cart->update(['user_id' => $userId]);
        }
        
        return $cart;
    }
    
    // Show cart contents
    public function index()
    {
        $cart = $this->getCart();
        
        // Eager load the product relationship for each cart item
        $cart->load('items.product');
        
        return view('cart.index', compact('cart'));
    }
    
    // Simplified Add to Cart method
    public function addToCart(Request $request)
    {
        // 1. Simple validation
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1); // Default to 1 if not provided
        
        if (!$productId || $quantity < 1) {
            return redirect()->back()->with('error', 'Invalid product or quantity');
        }
        
        // 2. Get the product
        $product = Product::find($productId);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }
        
        // 3. Get or create cart
        $cart = $this->getCart();
        
        // 4. Check if product is already in cart
        $cartItem = $cart->items()->where('product_id', $productId)->first();
        
        // 5. Update or create cart item
        if ($cartItem) {
            // Update quantity if product already exists in cart
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity
            ]);
        } else {
            // Add new item to cart
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
        
        // 6. Return with success message
        $message = $product->name . ' added to cart!';
        
        // If "keep shopping" was checked, stay on the products page
        if ($request->has('keep_shopping')) {
            return redirect()->back()->with('success', $message);
        }
        
        // Otherwise go to cart page
        return redirect()->route('cart.index')->with('success', $message);
    }
    
    // Update cart item quantity
    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cartItem->update(['quantity' => $validated['quantity']]);
        
        return redirect()->route('cart.index')->with('success', 'Cart updated!');
    }
    
    // Remove item from cart
    public function removeItem(CartItem $cartItem)
    {
        $cartItem->delete();
        
        return redirect()->route('cart.index')->with('success', 'Item removed from cart!');
    }
    
    // Clear entire cart
    public function clearCart()
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        
        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }
}
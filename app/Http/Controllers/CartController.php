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
    protected function getCart()
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
    
    // Add a product to cart
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cart = $this->getCart();
        
        // Check if product is already in cart
        $cartItem = $cart->items()->where('product_id', $validated['product_id'])->first();
        
        if ($cartItem) {
            // Update quantity if product already exists in cart
            $cartItem->update([
                'quantity' => $cartItem->quantity + $validated['quantity']
            ]);
        } else {
            // Add new item to cart
            $cart->items()->create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity']
            ]);
        }
        
        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
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
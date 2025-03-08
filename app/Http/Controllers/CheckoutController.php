<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function index()
    {
        // Get the cart
        $cartController = app(CartController::class);
        $cart = $cartController->getCart();
        
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty');
        }
        
        // Calculate shipping cost - simple version
        $shippingCost = $this->calculateShipping($cart);
        
        return view('checkout.index', compact('cart', 'shippingCost'));
    }
    
    // Calculate shipping based on number of items
    private function calculateShipping($cart)
    {
        $totalItems = $cart->items->sum('quantity');
        
        // Simple shipping calculation
        if ($totalItems <= 2) {
            return 5.00;
        } elseif ($totalItems <= 5) {
            return 8.00;
        } else {
            return 12.00;
        }
    }
    
    public function processCheckout(Request $request)
    {
        // Validate checkout form
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'zipcode' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:stripe,paypal',
            'notes' => 'nullable|string',
        ]);
        
        // Get cart
        $cartController = app(CartController::class);
        $cart = $cartController->getCart();
        
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty');
        }
        
        // Calculate shipping cost
        $shippingCost = $this->calculateShipping($cart);
        
        // Calculate cart total
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $subtotal += $item->quantity * $item->product->price;
        }
        
        $total = $subtotal + $shippingCost;
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Create the order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'shipping_name' => $request->name,
                'shipping_address' => $request->address,
                'shipping_city' => $request->city,
                'shipping_state' => $request->state,
                'shipping_zipcode' => $request->zipcode,
                'shipping_phone' => $request->phone,
                'shipping_email' => $request->email,
                'notes' => $request->notes,
            ]);
            
            // Add order items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'subtotal' => $item->quantity * $item->product->price,
                ]);
                
                // Update product stock
                $product = Product::find($item->product_id);
                $product->stock -= $item->quantity;
                $product->save();
            }
            
            // Process payment based on payment method
            if ($request->payment_method === 'stripe') {
                // Redirect to Stripe payment
                $session = $this->createStripeSession($order);
                
                // Commit the transaction
                DB::commit();
                
                // Redirect to Stripe Checkout
                return redirect($session->url);
            } elseif ($request->payment_method === 'paypal') {
                // Redirect to PayPal payment (implementation to be added)
                DB::commit();
                return redirect()->route('checkout.paypal', $order);
            }
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    private function createStripeSession($order)
    {
        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        
        $lineItems = [];
        
        // Add order items to Stripe checkout
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    'unit_amount' => $item->price * 100, // Amount in cents
                ],
                'quantity' => $item->quantity,
            ];
        }
        
        // Add shipping as a line item
        if ($order->shipping_cost > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Shipping Cost',
                    ],
                    'unit_amount' => $order->shipping_cost * 100, // Amount in cents
                ],
                'quantity' => 1,
            ];
        }
        
        // Create Stripe checkout session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', ['order' => $order->id, 'session_id' => '{CHECKOUT_SESSION_ID}']),
            'cancel_url' => route('checkout.cancel', ['order' => $order->id]),
        ]);
        
        // Update order with session ID
        $order->update(['payment_id' => $session->id]);
        
        return $session;
    }
    
    public function success(Request $request, Order $order)
    {
        // Verify the payment was successful
        if ($order->payment_status === 'pending' && $request->session_id) {
            // Update order status
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);
            
            // Clear the cart
            $cartController = app(CartController::class);
            $cartController->clearCart();
            
            return view('checkout.success', compact('order'));
        }
        
        return redirect()->route('products.index');
    }
    
    public function cancel(Order $order)
    {
        // Mark order as declined
        $order->update([
            'status' => 'declined',
            'payment_status' => 'failed',
        ]);
        
        return redirect()->route('checkout.index')->with('error', 'Payment was cancelled.');
    }
}
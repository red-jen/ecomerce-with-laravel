<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Checkout Form -->
                        <div>
                            <h2 class="text-lg font-semibold mb-4">Shipping Information</h2>
                            
                            <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required
                                           class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required
                                           class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Street Address</label>
                                    <input type="text" name="address" id="address" value="{{ old('address') }}" required
                                           class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                        <input type="text" name="city" id="city" value="{{ old('city') }}" required
                                               class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700">State/Province</label>
                                        <input type="text" name="state" id="state" value="{{ old('state') }}"
                                               class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="zipcode" class="block text-sm font-medium text-gray-700">Zip/Postal Code</label>
                                        <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode') }}" required
                                               class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                                               class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Order Notes (Optional)</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes') }}</textarea>
                                </div>
                                
                                <h2 class="text-lg font-semibold mb-4 mt-8">Payment Method</h2>
                                
                                <div class="mb-4">
                                    <div class="flex items-center space-x-4">
                                        <input id="stripe" name="payment_method" type="radio" value="stripe" checked
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        <label for="stripe" class="flex items-center">
                                            <span class="ml-2 block text-sm text-gray-900">Credit Card (Stripe)</span>
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Stripe_Logo%2C_revised_2016.svg/1200px-Stripe_Logo%2C_revised_2016.svg.png" 
                                                 alt="Stripe" class="h-8 ml-2">
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mt-8">
                                    <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Complete Order
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Order Summary -->
                        <div>
                            <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="mb-4">
                                    <h3 class="text-md font-medium mb-2">Items in Cart ({{ $cart->items->sum('quantity') }})</h3>
                                    
                                    <div class="space-y-2">
                                        @foreach ($cart->items as $item)
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center">
                                                    <span class="text-gray-600">{{ $item->quantity }} x</span>
                                                    <span class="ml-2">{{ $item->product->name }}</span>
                                                </div>
                                                <span class="font-medium">${{ number_format($item->quantity * $item->product->price, 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-4 mb-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="font-medium">${{ number_format($cart->items->sum(function($item) { return $item->quantity * $item->product->price; }), 2) }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600">Shipping</span>
                                        <span class="font-medium">${{ number_format($shippingCost, 2) }}</span>
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold">Total</span>
                                        <span class="text-lg font-bold">${{ number_format($cart->items->sum(function($item) { return $item->quantity * $item->product->price; }) + $shippingCost, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <a href="{{ route('cart.index') }}" class="text-indigo-600 hover:text-indigo-800">
                                    &larr; Back to Cart
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
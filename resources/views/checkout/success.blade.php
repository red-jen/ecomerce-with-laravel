<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Confirmation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-center mb-8">
                        <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke-width="2" stroke="currentColor" fill="none"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                        
                        <h2 class="mt-4 text-2xl font-bold text-gray-900">Thank You for Your Order!</h2>
                        <p class="mt-2 text-gray-600">Your order has been successfully placed and will be processed shortly.</p>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Order Details</h3>
                            <p class="text-gray-600">Order Number: <span class="font-medium">{{ $order->order_number }}</span></p>
                            <p class="text-gray-600">Date: <span class="font-medium">{{ $order->created_at->format('F j, Y') }}</span></p>
                            <p class="text-gray-600">Payment Method: <span class="font-medium">{{ ucfirst($order->payment_method) }}</span></p>
                            <p class="text-gray-600">Status: <span class="font-medium">{{ ucfirst($order->status) }}</span></p>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Shipping Address</h3>
                            <p class="text-gray-600">{{ $order->shipping_name }}</p>
                            <p class="text-gray-600">{{ $order->shipping_address }}</p>
                            <p class="text-gray-600">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zipcode }}</p>
                            <p class="text-gray-600">{{ $order->shipping_phone }}</p>
                            <p class="text-gray-600">{{ $order->shipping_email }}</p>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Order Summary</h3>
                            
                            <div class="border-t border-b border-gray-200 py-4">
                                @foreach ($order->items as $item)
                                    <div class="flex justify-between items-center py-2">
                                        <div>
                                            <span class="font-medium">{{ $item->product_name }}</span>
                                            <span class="text-gray-600 ml-2">x {{ $item->quantity }}</span>
                                        </div>
                                        <span class="font-medium">${{ number_format($item->subtotal, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="pt-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium">${{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="font-medium">${{ number_format($order->shipping_cost, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                                    <span class="text-lg font-bold">Total</span>
                                    <span class="text-lg font-bold">${{ number_format($order->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 text-center">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
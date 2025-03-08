<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $product->name }}
            </h2>
            <a href="{{ route('cart.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                View Cart 
                @if(isset($cart) && $cart->items->count() > 0)
                    <span class="ml-2 bg-white text-gray-800 rounded-full w-5 h-5 flex items-center justify-center">
                        {{ $cart->items->sum('quantity') }}
                    </span>
                @endif
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            @if($product->images->count() > 0)
                                <img src="{{ Storage::url($product->primaryImage ? $product->primaryImage->image_path : $product->images->first()->image_path) }}"
                                alt="{{ $product->name }}" 
                                class="w-full h-auto rounded-lg">
                            @else
                                <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-lg">
                                    <span class="text-gray-400">No image</span>
                                </div>
                            @endif
                            
                            @if($product->images->count() > 1)
                                <div class="mt-4 grid grid-cols-4 gap-2">
                                    @foreach($product->images as $image)
                                        <img src="{{ Storage::url($image->image_path) }}" 
                                             alt="{{ $product->name }}"
                                             class="w-full h-20 object-cover rounded cursor-pointer">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                            
                            <div class="mt-4">
                                <span class="text-2xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-gray-600">{{ $product->description }}</p>
                            </div>
                            
                            <div class="mt-6">
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    
                                    <div class="flex items-center mb-4">
                                        <label for="quantity" class="block mr-4 font-medium text-gray-700">Quantity:</label>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="99"
                                               class="w-20 rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Add to Cart
                                    </button>
                                </form>
                            </div>
                            
                            <div class="mt-6">
                                <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-800">
                                    &larr; Back to Products
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($relatedProducts->count() > 0)
                <div class="mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Related Products</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                @if($relatedProduct->images->count() > 0)
                                    <img src="{{ Storage::url($relatedProduct->primaryImage ? $relatedProduct->primaryImage->image_path : $relatedProduct->images->first()->image_path) }}"
                                    alt="{{ $relatedProduct->name }}" 
                                    class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400">No image</span>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $relatedProduct->name }}</h3>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-xl font-bold text-gray-900">${{ number_format($relatedProduct->price, 2) }}</span>
                                        <a href="{{ route('products.show', $relatedProduct) }}" class="text-indigo-600 hover:text-indigo-800">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>


<div class="mt-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Customer Reviews</h2>
    
    @if($product->reviews->count() > 0)
        <div class="space-y-4">
            @foreach($product->reviews as $review)
                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $review->title }}</h3>
                            <div class="flex items-center text-yellow-400 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 24 24">
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <p class="text-gray-600">{{ $review->comment }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                By {{ $review->user->name }} on {{ $review->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        
                        @if(auth()->check() && (auth()->id() === $review->user_id || auth()->user()->is_admin))
                            <form action="{{ route('reviews.destroy', $review) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-600">No reviews yet. Be the first to review this product!</p>
    @endif
    
    @auth
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4">Write a Review</h3>
            
            <form action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" required
                           class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                
                <div class="mb-4">
                    <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                    <select name="rating" id="rating" required
                            class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Very Good</option>
                        <option value="3">3 - Good</option>
                        <option value="2">2 - Fair</option>
                        <option value="1">1 - Poor</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700">Your Review</label>
                    <textarea name="comment" id="comment" rows="4" required
                              class="mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md py-2 px-4 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Submit Review
                </button>
            </form>
        </div>
    @else
        <div class="mt-6 bg-gray-100 rounded-lg p-4">
            <p class="text-center text-gray-600">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800">Log in</a> to write a review.
            </p>
        </div>
    @endauth
</div>
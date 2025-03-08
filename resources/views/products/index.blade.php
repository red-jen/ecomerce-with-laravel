<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Products') }}
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
            
            <div class="mb-6 flex space-x-2">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-3 py-1 bg-gray-200 rounded-md {{ !request('category') ? 'font-bold bg-indigo-100' : '' }}">
                    All
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->id]) }}" class="inline-flex items-center px-3 py-1 bg-gray-200 rounded-md {{ request('category') == $category->id ? 'font-bold bg-indigo-100' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        @if($product->images->count() > 0)
                            <img src="{{ Storage::url($product->primaryImage ? $product->primaryImage->image_path : $product->images->first()->image_path) }}"
                            alt="{{ $product->name }}" 
                            class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No image</span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                            <p class="mt-1 text-gray-600 truncate">{{ Str::limit($product->description, 100) }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            </div>
                            
                            <div class="mt-4 flex flex-col space-y-2">
                                <form action="{{ route('cart.add') }}" method="POST" class="flex flex-col space-y-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <div class="flex items-center">
                                        <input type="number" name="quantity" value="1" min="1" max="99" class="w-16 rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <input type="checkbox" name="keep_shopping" id="keep-shopping-{{ $product->id }}" value="1" class="ml-2">
                                        <label for="keep-shopping-{{ $product->id }}" class="ml-1 text-sm text-gray-600">Stay on page</label>
                                    </div>
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        Add to Cart
                                    </button>
                                </form>
                                
                                <a href="{{ route('products.show', $product) }}" class="text-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($products->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <p class="text-gray-500">No products found</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
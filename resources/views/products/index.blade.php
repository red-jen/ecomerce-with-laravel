<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                        <!-- Inside the product loop -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($product->images->count() > 0)
        {{-- <img src="{{ Storage::url($product->primaryImage ? $product->primaryImage->path : $product->images->first()->path) }}" 
             alt="{{ $product->name }}" 
             class="w-full h-48 object-cover"> --}}
             <img src="{{ Storage::url($product->primaryImage ? $product->primaryImage->image_path : $product->images->first()->image_path) }}"
             alt="{{ $product->name }}" 
             class="w-full h-48 object-cover">
             @else
        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
            <span class="text-gray-400">No image</span>
        </div>
    @endif
    <div class="p-4">
        <!-- Existing content -->
    </div>
</div>
                        <div class="p-4">/
                            <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                            <p class="mt-1 text-gray-600 truncate">{{ Str::limit($product->description, 100) }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                                        Add to Cart
                                    </button>
                                </form>
                            </div>
                            <div class="mt-2 text-sm text-gray-600">
                                <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-800">
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
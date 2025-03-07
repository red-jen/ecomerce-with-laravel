<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.products.update', $product) }}"enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mt-4">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $product->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $product->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div class="mt-4">
                            <x-input-label for="price" :value="__('Price')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price', $product->price)" step="0.01" min="0" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Stock -->
                        <div class="mt-4">
                            <x-input-label for="stock" :value="__('Stock')" />
                            <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" :value="old('stock', $product->stock)" min="0" required />
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div class="mt-4">
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select id="category_id" name="category_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>


                         <!-- Current Product Images -->
                         @if($product->images->count() > 0)
                         <div class="mt-4">
                             <x-input-label :value="__('Current Images')" />
                             <div class="flex flex-wrap gap-4 mt-2">
                                 @foreach($product->images as $image)
                                     <div class="relative group">
                                         <img src="{{ Storage::url($image->image_path) }}" alt="{{ $product->name }}" class="h-24 w-24 object-cover rounded">
                                         <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                             <button type="button" 
                                                 onclick="document.getElementById('delete-image-{{ $image->id }}').submit();"
                                                 class="text-white hover:text-red-500">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                 </svg>
                                             </button>
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                         </div>
                     @endif

                     <!-- Add New Images -->
                     <div class="mt-4">
                         <x-input-label for="images" :value="__('Add New Images')" />
                         <input type="file" id="images" name="images[]" multiple class="block mt-1 w-full" accept="image/*" />
                         <p class="mt-1 text-sm text-gray-500">You can select multiple images. Supported formats: JPG, PNG, GIF</p>
                         <x-input-error :messages="$errors->get('images')" class="mt-2" />
                         <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
                     </div>


                        <!-- Active Status -->
                        <div class="mt-4">
                            <label for="is_active" class="inline-flex items-center">
                                <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Active') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                                Cancel
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Update Product') }}
                            </x-primary-button>
                        </div>
                    </form>
                       <!-- Hidden forms for image deletion -->
                       @foreach($product->images as $image)
                       <form id="delete-image-{{ $image->id }}" action="{{ route('admin.product-images.destroy', $image) }}" method="POST" class="hidden">
                           @csrf
                           @method('DELETE')
                       </form>
                   @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
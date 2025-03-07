<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function destroy(ProductImage $productImage)
    {
        $productId = $productImage->product_id;
        $isPrimary = $productImage->is_primary;
        
        // Delete the file
        Storage::disk('public')->delete($productImage->image_path); // Changed from 'path' to 'image_path'
        
        // Delete the record
        $productImage->delete();
        
        // If this was the primary image, set another one as primary (if any exist)
        if ($isPrimary) {
            $newPrimary = ProductImage::where('product_id', $productId)->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }
        
        return back()->with('success', 'Image deleted successfully');
    }
}
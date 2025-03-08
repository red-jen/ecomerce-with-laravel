<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|max:255',
            'comment' => 'required',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = Review::create([
            'product_id' => $request->product_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'is_approved' => true, // Set to false if you want moderation
        ]);

        return redirect()->back()->with('success', 'Your review has been submitted!');
    }

    public function destroy(Review $review)
    {
        // Check if the user is authorized to delete this review
        if (auth()->id() === $review->user_id || auth()->user()->is_admin) {
            $review->delete();
            return redirect()->back()->with('success', 'Review deleted successfully');
        }

        return redirect()->back()->with('error', 'You are not authorized to delete this review');
    }

    
}
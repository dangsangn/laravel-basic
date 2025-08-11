<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backend\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function showAllReviews() {
        $reviews = Review::latest()->get();
        return view('admin.reviews.index', compact('reviews'));
    }
}

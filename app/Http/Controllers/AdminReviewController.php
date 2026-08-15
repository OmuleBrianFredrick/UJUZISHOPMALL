<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless(($request->user()->role ?? null) === 'admin', 403);
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin($request);
        $reviews = Review::with(['user','product'])->latest()->paginate(25);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(Request $request, Review $review)
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['status' => ['required','in:pending,approved,rejected']]);
        $review->update(['status' => $data['status']]);
        return back()->with('success', 'Review moderation status updated.');
    }
}

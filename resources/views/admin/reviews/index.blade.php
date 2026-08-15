@extends('layouts.dashboard')
@section('title','Review Moderation — Ujuzi Shop Mall')
@section('content')
<div class="dashboard-panel">
    <div class="panel-head"><div><h1 class="panel-title">Review Moderation</h1><p class="panel-note">Approve or reject customer reviews before they appear publicly.</p></div></div>
    @forelse($reviews as $review)
        <article style="padding:16px 0;border-bottom:1px solid #e5e7eb;">
            <div style="display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div><strong>{{ $review->product->name ?? 'Deleted product' }}</strong><div>{{ $review->user->name ?? 'Customer' }} · {{ $review->rating }}/5 · {{ $review->verified_purchase ? 'Verified purchase' : 'Unverified' }}</div></div>
                <span>{{ ucfirst($review->status) }}</span>
            </div>
            <p>{{ $review->body }}</p>
            <form method="POST" action="{{ route('admin.reviews.update',$review) }}" style="display:flex;gap:8px;align-items:center;">
                @csrf @method('PATCH')
                <select name="status"><option value="pending" @selected($review->status==='pending')>Pending</option><option value="approved" @selected($review->status==='approved')>Approved</option><option value="rejected" @selected($review->status==='rejected')>Rejected</option></select>
                <button class="btn-solid" type="submit">Save status</button>
            </form>
        </article>
    @empty
        <p>No reviews are waiting for moderation.</p>
    @endforelse
    {{ $reviews->links() }}
</div>
@endsection

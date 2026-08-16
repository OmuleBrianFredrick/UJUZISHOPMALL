@php($reviews = $approvedReviews)
<section class="dashboard-panel" style="margin-top:28px;">
    <div class="panel-head"><div><h2 class="panel-title">Customer reviews</h2><p class="panel-note">Verified purchase reviews</p></div></div>
    @forelse($reviews as $review)
        <article style="padding:14px 0;border-bottom:1px solid #e5e7eb;">
            <strong>{{ $review->user->name ?? 'Customer' }}</strong>
            <div aria-label="{{ $review->rating }} out of 5 stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
            <p>{{ $review->body }}</p>
            @if($review->verified_purchase)<small>Verified purchase</small>@endif
        </article>
    @empty
        <p>No approved reviews yet.</p>
    @endforelse
</section>

@extends('layouts.dashboard')
@section('title','Seller Applications')
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Admin marketplace</div><h1 class="dashboard-title">Seller applications</h1><p class="dashboard-subtitle">Approve or reject stores before they can sell.</p></div></div>
<section class="dashboard-panel"><table class="modern-table"><thead><tr><th>Store</th><th>Owner</th><th>Location</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($profiles as $profile)<tr><td><strong>{{ $profile->store_name }}</strong><span class="product-sub">{{ $profile->phone }}</span></td><td>{{ $profile->user->name }}<span class="product-sub">{{ $profile->user->email }}</span></td><td>{{ $profile->location ?: '—' }}</td><td><span class="status-badge {{ $profile->status === 'approved' ? 'status-ok' : 'status-low' }}">{{ ucfirst($profile->status) }}</span></td><td><div class="action-links">@if($profile->status !== 'approved')<form method="POST" action="{{ route('admin.sellers.approve',$profile) }}" style="display:inline">@csrf @method('PATCH')<button class="btn-sm btn-solid" type="submit">Approve</button></form>@endif @if($profile->status !== 'rejected')<form method="POST" action="{{ route('admin.sellers.reject',$profile) }}" style="display:inline">@csrf @method('PATCH')<button class="btn-sm btn-outline-dark" type="submit">Reject</button></form>@endif</div></td></tr>@empty<tr><td colspan="5">No seller applications.</td></tr>@endforelse
</tbody></table></section>
<div class="storefront-pagination">{{ $profiles->links() }}</div>
@endsection

@extends('layouts.dashboard')
@section('title','Become a Seller')
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Marketplace</div><h1 class="dashboard-title">Become a seller</h1><p class="dashboard-subtitle">Create your store and submit it for admin approval.</p></div></div>
<section class="dashboard-panel user-form">
@if($profile)<div class="status-badge status-{{ $profile->status === 'approved' ? 'ok' : 'low' }}" style="margin-bottom:18px">Application: {{ ucfirst($profile->status) }}</div>@endif
<form method="POST" action="{{ route('seller.apply.store') }}">@csrf
<div class="form-group"><label>Store name</label><input name="store_name" value="{{ old('store_name',$profile?->store_name) }}" required></div>
<div class="form-group"><label>Store description</label><textarea name="description" rows="4" style="width:100%;padding:11px;border:1px solid #e7ebf2;border-radius:10px">{{ old('description',$profile?->description) }}</textarea></div>
<div class="form-group"><label>Phone</label><input name="phone" value="{{ old('phone',$profile?->phone) }}" required></div>
<div class="form-group"><label>Location</label><input name="location" value="{{ old('location',$profile?->location) }}"></div>
<button class="btn-solid" type="submit">Submit Seller Application</button>
</form></section>
@endsection

@extends('layouts.app')
@section('content')
<div class="container py-4"><h1>My Wishlist</h1><div class="row g-3">@forelse($items as $item)<div class="col-md-4"><div class="card h-100 p-3"><h5>{{ $item->product->name }}</h5><p>UGX {{ number_format($item->product->price) }}</p><form method="POST" action="{{ route('wishlist.toggle',$item->product) }}">@csrf<button class="btn btn-outline-danger">Remove</button></form></div></div>@empty<p>Your wishlist is empty.</p>@endforelse</div></div>
@endsection

@extends('layouts.app')
@section('content')
<div class="container py-4"><h1>Loyalty Rewards</h1><div class="alert alert-primary"><strong>{{ number_format($balance) }}</strong> points available</div><h3>Activity</h3>@forelse($transactions as $tx)<div class="border rounded p-3 mb-2"><strong>{{ $tx->points > 0 ? '+' : '' }}{{ $tx->points }} points</strong><div>{{ $tx->description }}</div><small>{{ $tx->created_at->format('d M Y H:i') }}</small></div>@empty<p>No loyalty activity yet.</p>@endforelse{{ $transactions->links() }}</div>
@endsection

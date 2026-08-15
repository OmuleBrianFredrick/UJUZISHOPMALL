@extends('layouts.dashboard')
@section('title','Loyalty Rewards — Ujuzi Shop Mall')
@section('content')
<div class="dashboard-hero"><div><div class="dashboard-kicker">Rewards</div><h1 class="dashboard-title">Loyalty Rewards</h1><p class="dashboard-subtitle">Earn points from completed purchases and use them toward future orders.</p></div></div>
<div class="dashboard-panel" style="margin-bottom:20px;"><div class="panel-head"><div><div class="panel-note">Available balance</div><div style="font-size:36px;font-weight:800;">{{ number_format($balance) }} points</div><p class="panel-note">Redemption value: UGX {{ number_format($balance * 10) }}</p></div><div style="font-size:42px;">🏆</div></div></div>
<div class="dashboard-panel"><div class="panel-head"><div><h2 class="panel-title">Points activity</h2><p class="panel-note">Every earning and redemption is recorded in your loyalty ledger.</p></div></div>
@forelse($transactions as $tx)
<article style="display:flex;justify-content:space-between;gap:16px;padding:14px 0;border-bottom:1px solid #e5e7eb;">
 <div><strong>{{ $tx->points > 0 ? 'Points earned' : 'Points redeemed' }}</strong><div>{{ $tx->description }}</div><small>{{ $tx->created_at->format('d M Y H:i') }}</small></div>
 <strong>{{ $tx->points > 0 ? '+' : '' }}{{ number_format($tx->points) }}</strong>
</article>
@empty
<p>No loyalty activity yet. Complete a purchase to start earning rewards.</p>
@endforelse
{{ $transactions->links() }}
</div>
@endsection

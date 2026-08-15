@extends('layouts.dashboard')
@section('title','Promotion Management — Ujuzi Shop Mall')
@section('content')
<div class="dashboard-panel">
    <div class="panel-head"><div><h1 class="panel-title">Promotion Management</h1><p class="panel-note">Create controlled discounts and activate or pause campaigns.</p></div></div>
    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert-error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('admin.promotions.store') }}" class="dashboard-panel" style="margin-bottom:20px;">
        @csrf
        <h2 class="panel-title">Create promotion</h2>
        <div class="form-grid">
            <label>Code<input name="code" value="{{ old('code') }}" maxlength="50" required placeholder="WELCOME10"></label>
            <label>Type<select name="type" required><option value="percentage">Percentage</option><option value="fixed">Fixed UGX</option></select></label>
            <label>Value<input name="value" type="number" step="0.01" min="0.01" required></label>
            <label>Minimum order<input name="minimum_order" type="number" step="0.01" min="0"></label>
            <label>Usage limit<input name="usage_limit" type="number" min="1"></label>
            <label>Starts at<input name="starts_at" type="datetime-local"></label>
            <label>Ends at<input name="ends_at" type="datetime-local"></label>
            <label style="display:flex;align-items:center;gap:8px;margin-top:28px;"><input name="active" type="checkbox" value="1"> Activate immediately</label>
        </div>
        <button class="btn-solid" type="submit">Create promotion</button>
    </form>
    <div style="overflow:auto;">
        <table class="data-table" style="width:100%;">
            <thead><tr><th>Code</th><th>Discount</th><th>Minimum</th><th>Usage</th><th>Validity</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($promotions as $promotion)
                <tr>
                    <td><strong>{{ $promotion->code }}</strong></td>
                    <td>{{ $promotion->type === 'percentage' ? $promotion->value.'%' : 'UGX '.number_format($promotion->value,2) }}</td>
                    <td>UGX {{ number_format($promotion->minimum_order,2) }}</td>
                    <td>{{ $promotion->usage_count }} / {{ $promotion->usage_limit ?: '∞' }}</td>
                    <td>{{ $promotion->starts_at?->format('d M Y H:i') ?: 'Now' }} — {{ $promotion->ends_at?->format('d M Y H:i') ?: 'No expiry' }}</td>
                    <td>{{ $promotion->active ? 'Active' : 'Paused' }}</td>
                    <td><form method="POST" action="{{ route('admin.promotions.toggle',$promotion) }}">@csrf @method('PATCH')<button class="btn-outline" type="submit">{{ $promotion->active ? 'Pause' : 'Activate' }}</button></form></td>
                </tr>
            @empty
                <tr><td colspan="7">No promotions have been created.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $promotions->links() }}
</div>
@endsection

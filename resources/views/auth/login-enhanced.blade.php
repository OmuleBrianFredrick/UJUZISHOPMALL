@extends('layouts.dashboard')
@section('title', 'Login — Ujuzi Shop Mall')
@section('content')
<div class="auth-wrap">
    <h1 class="page-heading" style="font-size:26px;text-align:center;">Sign in to Ujuzi Shop Mall</h1>
    <div class="card-panel">
        @if($errors->any())<div class="alert-error">{{ $errors->first() }}</div>@endif
        <p style="text-align:center;color:var(--muted);font-size:13px;">Customers can sign in normally. Inventory managers and administrators complete an email OTP verification after their password is accepted.</p>
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="form-group"><label>Email Address</label><input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" autocomplete="current-password" required></div>
            <div class="form-group" style="display:flex;align-items:center;gap:8px;"><input type="checkbox" name="remember" id="remember" style="width:auto;"><label for="remember" style="margin:0;font-weight:400;">Remember me</label></div>
            <button type="submit" class="btn-solid" style="width:100%;">Sign in</button>
        </form>
        <div style="display:flex;align-items:center;gap:10px;margin:22px 0;color:var(--warm-gray);font-size:12px;"><span style="height:1px;background:var(--border-tan);flex:1"></span>OR<span style="height:1px;background:var(--border-tan);flex:1"></span></div>
        <a href="{{ route('auth.google') }}" class="btn-outline-dark" style="width:100%;display:block;text-align:center;text-decoration:none;"><i class="fa-brands fa-google"></i> Continue with Google</a>
        <p style="text-align:center;margin-top:18px;font-size:13px;color:var(--muted);">Don't have an account? <a href="{{ route('register') }}" style="color:var(--accent);">Register here</a></p>
    </div>
</div>
@endsection

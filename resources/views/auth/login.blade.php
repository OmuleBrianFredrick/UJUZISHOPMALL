@extends('layouts.dashboard')
@section('title', 'Login — Ujuzi Shop Mall')
@section('content')
    <div class="auth-wrap">
        <h1 class="page-heading" style="font-size:26px; text-align:center;">Staff Login</h1>
        <div class="card-panel">
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group" style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="remember" id="remember" style="width:auto;">
                    <label for="remember" style="margin:0; font-weight:400;">Remember me</label>
                </div>
                <button type="submit" class="btn-solid" style="width:100%;">Login</button>
            </form>
            <p style="text-align:center; margin-top:18px; font-size:13px; color:var(--muted);">
                Don't have an account? <a href="{{ route('register') }}" style="color:var(--accent);">Register here</a>
            </p>
        </div>
    </div>
@endsection

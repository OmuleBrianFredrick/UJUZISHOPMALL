@extends('layouts.dashboard')
@section('title', 'Register — Ujuzi Shop Mall')
@section('content')
    <div class="auth-wrap">
        <h1 class="page-heading" style="font-size:26px; text-align:center;">Create Staff Account</h1>
        <div class="card-panel">
            <form method="POST" action="{{ route('register.submit') }}">
                @csrf
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" required>
                </div>
                <button type="submit" class="btn-solid" style="width:100%;">Create Account</button>
            </form>
            <p style="text-align:center; margin-top:18px; font-size:13px; color:var(--muted);">
                Already have an account? <a href="{{ route('login') }}" style="color:var(--accent);">Login here</a>
            </p>
        </div>
    </div>
@endsection

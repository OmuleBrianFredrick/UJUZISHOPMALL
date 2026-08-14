@extends('layouts.dashboard')
@section('title', 'Staff Verification — Ujuzi Shop Mall')
@section('content')
<div class="auth-wrap">
    <h1 class="page-heading" style="font-size:26px;text-align:center;">Verify Staff Sign-in</h1>
    <div class="card-panel">
        @if(session('otp_sent'))<div class="alert-success">{{ session('otp_sent') }}</div>@endif
        <p style="text-align:center;color:var(--muted);">Your password was accepted. Enter the 6-digit OTP sent to your registered email address to complete sign-in.</p>
        @if($errors->any())<div class="alert-error">{{ $errors->first('code') }}</div>@endif
        <form method="POST" action="{{ route('login.otp.verify') }}">
            @csrf
            <div class="form-group"><label>Email verification code</label><input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" required autofocus></div>
            <button type="submit" class="btn-solid" style="width:100%;">Verify & Continue</button>
        </form>
        <form method="POST" action="{{ route('login.otp.request') }}" style="margin-top:10px;">
            @csrf
            <button type="submit" class="btn-outline-dark" style="width:100%;">Send a new code</button>
        </form>
        <p style="text-align:center;margin-top:18px;font-size:13px;"><a href="{{ route('login') }}">Return to login</a></p>
    </div>
</div>
@endsection

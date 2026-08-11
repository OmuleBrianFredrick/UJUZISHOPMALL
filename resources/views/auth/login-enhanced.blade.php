@extends('layouts.dashboard')
@section('title', 'Login — Ujuzi Shop Mall')
@section('content')
<div class="auth-wrap">
    <h1 class="page-heading" style="font-size:26px;text-align:center;">Staff Login</h1>
    <div class="card-panel">
        @if(session('otp_sent'))<div class="alert-success">{{ session('otp_sent') }}</div>@endif
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:20px;"><button type="button" id="passwordTab" class="btn-solid">Password</button><button type="button" id="otpTab" class="btn-outline-dark">Phone OTP</button></div>
        <div id="passwordPanel">
            <form method="POST" action="{{ route('login.submit') }}">@csrf
                <div class="form-group"><label>Email Address</label><input type="email" name="email" value="{{ old('email') }}" required></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                <div class="form-group" style="display:flex;align-items:center;gap:8px;"><input type="checkbox" name="remember" id="remember" style="width:auto;"><label for="remember" style="margin:0;font-weight:400;">Remember me</label></div>
                <button type="submit" class="btn-solid" style="width:100%;">Login</button>
            </form>
        </div>
        <div id="otpPanel" style="display:none;">
            <form method="POST" action="{{ route('login.otp.request') }}">@csrf
                <div class="form-group"><label>Uganda phone number</label><input type="text" name="phone" placeholder="07XX XXX XXX or +2567XX XXX XXX" required></div>
                <button type="submit" class="btn-solid" style="width:100%;">Send OTP</button>
            </form>
            <div style="height:1px;background:var(--border-tan);margin:20px 0;"></div>
            <form method="POST" action="{{ route('login.otp.verify') }}">@csrf
                <div class="form-group"><label>Phone number</label><input type="text" name="phone" placeholder="+2567XX XXX XXX" required></div>
                <div class="form-group"><label>6-digit verification code</label><input type="text" name="code" inputmode="numeric" maxlength="6" required></div>
                <button type="submit" class="btn-solid" style="width:100%;">Verify & Login</button>
            </form>
        </div>
        <div style="display:flex;align-items:center;gap:10px;margin:22px 0;color:var(--warm-gray);font-size:12px;"><span style="height:1px;background:var(--border-tan);flex:1"></span>OR<span style="height:1px;background:var(--border-tan);flex:1"></span></div>
        <a href="{{ route('auth.google') }}" class="btn-outline-dark" style="width:100%;display:block;text-align:center;text-decoration:none;"><i class="fa-brands fa-google"></i> Continue with Google</a>
        <p style="text-align:center;margin-top:18px;font-size:13px;color:var(--muted);">Don't have an account? <a href="{{ route('register') }}" style="color:var(--accent);">Register here</a></p>
    </div>
</div>
@endsection
@push('scripts')
<script>
const pT=document.getElementById('passwordTab'),oT=document.getElementById('otpTab'),pP=document.getElementById('passwordPanel'),oP=document.getElementById('otpPanel');
pT.addEventListener('click',()=>{pP.style.display='block';oP.style.display='none';pT.className='btn-solid';oT.className='btn-outline-dark';});
oT.addEventListener('click',()=>{pP.style.display='none';oP.style.display='block';oT.className='btn-solid';pT.className='btn-outline-dark';});
</script>
@endpush

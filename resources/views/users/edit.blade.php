@extends('layouts.dashboard')
@section('title', 'Edit User — Ujuzi Shop Mall')
@section('content')
<div class="dashboard-hero">
    <div><div class="dashboard-kicker">Access management</div><h1 class="dashboard-title">Edit user</h1><p class="dashboard-subtitle">Update {{ $user->name }}'s account details. Leave the password blank to keep the current password.</p></div>
    <a href="{{ route('users.index') }}" class="btn-outline-dark"><i class="fa-solid fa-arrow-left"></i> Back to Users</a>
</div>

<section class="dashboard-panel user-form">
    <form method="POST" action="{{ route('users.update',$user) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name',$user->name) }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email',$user->email) }}" required>
        </div>
        <div class="form-group">
            <label for="password">New password</label>
            <input id="password" type="password" name="password" minlength="8" autocomplete="new-password" placeholder="Leave blank to keep current password">
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password">
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:22px;">
            <a href="{{ route('users.index') }}" class="btn-outline-dark">Cancel</a>
            <button type="submit" class="btn-solid"><i class="fa-solid fa-floppy-disk"></i> Save changes</button>
        </div>
    </form>
</section>
@endsection

@extends('layouts.dashboard')
@section('title', 'Users — Ujuzi Shop Mall')
@section('content')
<div class="dashboard-hero">
    <div><div class="dashboard-kicker">Access management</div><h1 class="dashboard-title">Users</h1><p class="dashboard-subtitle">Review staff accounts and update profile details securely.</p></div>
</div>

<section class="dashboard-panel dashboard-table-panel">
    <div class="table-toolbar">
        <div><h2 class="panel-title">User directory</h2><p class="panel-note">{{ $users->count() }} registered account{{ $users->count() === 1 ? '' : 's' }}</p></div>
        <input class="table-search" id="userSearch" type="search" placeholder="Search users…">
    </div>
    <table class="modern-table users-table" id="usersTable">
        <thead><tr><th>User</th><th>Email</th><th>Joined</th><th>Last updated</th><th>Action</th></tr></thead>
        <tbody>
        @forelse($users as $user)
            <tr data-search="{{ strtolower($user->name.' '.$user->email) }}">
                <td><div class="product-cell"><div class="avatar">{{ strtoupper(substr($user->name,0,1)) }}</div><div><strong>{{ $user->name }}</strong><span class="product-sub">Account #{{ $user->id }}</span></div></div></td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->created_at?->format('d M Y') }}</td>
                <td>{{ $user->updated_at?->diffForHumans() }}</td>
                <td><a href="{{ route('users.edit',$user) }}" class="btn-outline-dark btn-sm"><i class="fa-solid fa-pen"></i> Edit</a></td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--dash-muted);">No user accounts found.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
@endsection

@push('scripts')
<script>
const userSearch = document.getElementById('userSearch');
if (userSearch) userSearch.addEventListener('input', function(){ const term=this.value.toLowerCase(); document.querySelectorAll('#usersTable tbody tr[data-search]').forEach(r=>r.style.display=r.dataset.search.includes(term)?'':'none'); });
</script>
@endpush

@extends('layouts.admin')
@section('title', 'Users')
@section('page-title', 'User Management')
@section('content')
<div class="admin-heading"><div><h1>System Users</h1><p>Create accounts, assign newsroom roles and manage access.</p></div></div>

<div class="admin-stats compact">
    @foreach([['users','All users',$stats['all']],['user-shield','Administrators',$stats['admin']],['user-pen','Editors',$stats['editor']],['user','Authors',$stats['author']]] as $stat)
        <div class="admin-stat"><i class="fas fa-{{ $stat[0] }}"></i><div><b>{{ number_format($stat[2]) }}</b><small>{{ $stat[1] }}</small></div></div>
    @endforeach
</div>

<div class="admin-two-col users-layout">
    <section class="admin-card sticky-card">
        <div class="admin-card-title"><i class="fas fa-user-plus"></i> Add New User</div>
        <form class="admin-form" method="post" action="{{ route('dashboard.users.store') }}">
            @csrf
            <label>Full Name *<input name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name"></label>
            <label>Email Address *<input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
            <label>Password *<input type="password" name="password" minlength="8" required autocomplete="new-password"><small class="field-note">Minimum 8 characters.</small></label>
            <label>Confirm Password *<input type="password" name="password_confirmation" minlength="8" required autocomplete="new-password"></label>
            <label>User Role<select name="role"><option value="author">Author — own posts</option><option value="editor">Editor — all content</option><option value="admin">Administrator — full access</option></select></label>
            <button class="admin-btn"><i class="fas fa-user-plus"></i>Create User</button>
        </form>
    </section>

    <div>
        <section class="admin-card">
            <form class="admin-filters simple" method="get" action="{{ route('dashboard.users') }}">
                <label class="filter-search"><i class="fas fa-search"></i><input name="search" value="{{ request('search') }}" placeholder="Search name or email"></label>
                <select name="role"><option value="">All roles</option>@foreach(['admin','editor','author'] as $role)<option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>@endforeach</select>
                <button class="admin-btn"><i class="fas fa-filter"></i>Filter</button>
                @if(request()->hasAny(['search','role']))<a class="filter-clear" href="{{ route('dashboard.users') }}">Clear</a>@endif
            </form>
        </section>

        <section class="admin-card">
            <div class="admin-card-title"><i class="fas fa-address-book"></i> User directory</div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>User</th><th>Role</th><th>Posts</th><th>Joined</th><th>Manage</th></tr></thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><div class="comment-reader"><span class="comment-reader-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span><div><b>{{ $user->name }}</b><br><small>{{ $user->email }}</small></div></div></td>
                            <td><span class="status role-{{ $user->role }}">{{ $user->role }}</span></td>
                            <td>{{ $user->post_count }}</td>
                            <td>{{ $user->created_at?->format('M d, Y') }}</td>
                            <td>
                                <details class="user-editor">
                                    <summary><i class="fas fa-pen"></i> Edit</summary>
                                    <div class="user-editor-panel">
                                        <form method="post" action="{{ route('dashboard.users.update', $user) }}" class="admin-form compact-form">
                                            @csrf @method('patch')
                                            <label>Name<input name="name" value="{{ $user->name }}" required></label>
                                            <label>Email<input type="email" name="email" value="{{ $user->email }}" required></label>
                                            <label>Role<select name="role">@foreach(['author','editor','admin'] as $role)<option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>@endforeach</select></label>
                                            <label>New password <input type="password" name="password" minlength="8" placeholder="Leave blank to keep current"></label>
                                            <label>Confirm new password <input type="password" name="password_confirmation" minlength="8"></label>
                                            <button class="admin-btn" type="submit"><i class="fas fa-save"></i>Save user</button>
                                        </form>
                                        <form method="post" action="{{ route('dashboard.users.destroy', $user) }}" class="danger-zone" onsubmit="return confirm('Delete this user and all posts owned by them?')">
                                            @csrf @method('delete')
                                            <button type="submit" @disabled((string) $user->_id === (string) auth()->id())><i class="fas fa-trash"></i> Delete user and their posts</button>
                                        </form>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-admin">No users match your filters.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection

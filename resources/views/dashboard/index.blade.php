@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')
@section('content')
<div class="admin-heading">
    <div>
        <h1>Welcome back, {{ auth()->user()->name }}!</h1>
        <p>{{ auth()->user()->role === 'author' ? 'Track your stories and publishing activity.' : 'Here is what is happening across the newsroom today.' }}</p>
    </div>
    <a class="admin-btn" href="{{ route('dashboard.posts.create') }}"><i class="fas fa-plus"></i>Create Post</a>
</div>

<div class="admin-stats">
    @foreach($stats as $stat)
        <div class="admin-stat"><i class="fas fa-{{ $stat[0] }}"></i><div><b>{{ number_format($stat[2]) }}</b><small>{{ $stat[1] }}</small></div></div>
    @endforeach
</div>

<section class="admin-card">
    <div class="admin-card-title admin-card-title-row">
        <span><i class="fas fa-clock"></i> Recent Posts</span>
        <a href="{{ route('dashboard.posts.index') }}">Manage all <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Post Title</th><th>Status</th><th>Views</th><th>Last Updated</th><th></th></tr></thead>
            <tbody>
            @forelse($posts as $post)
                <tr>
                    <td><b>{{ $post->title }}</b></td>
                    <td><span class="status status-{{ $post->status }}">{{ $post->status }}</span></td>
                    <td>{{ number_format($post->views ?? 0) }}</td>
                    <td>{{ $post->updated_at?->diffForHumans() }}</td>
                    <td><a class="table-link" href="{{ route('dashboard.posts.edit', $post) }}">Edit <i class="fas fa-arrow-right"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-admin">No posts yet. Create your first story.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

@extends('layouts.admin')
@section('title', 'Comments')
@section('page-title', 'Comment Moderation')
@section('content')
<div class="admin-heading"><div><h1>Reader Comments</h1><p>Review discussions and keep the community constructive.</p></div></div>

<div class="admin-stats compact">
    @foreach([['comments','All comments',$stats['all']],['clock','Pending',$stats['pending']],['circle-check','Approved',$stats['approved']],['circle-xmark','Rejected',$stats['rejected']]] as $stat)
        <div class="admin-stat"><i class="fas fa-{{ $stat[0] }}"></i><div><b>{{ number_format($stat[2]) }}</b><small>{{ $stat[1] }}</small></div></div>
    @endforeach
</div>

<section class="admin-card">
    <form class="admin-filters simple" method="get" action="{{ route('dashboard.comments') }}">
        <label class="filter-search"><i class="fas fa-search"></i><input name="search" value="{{ request('search') }}" placeholder="Search reader, email or comment"></label>
        <select name="status"><option value="">All statuses</option>@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
        <button class="admin-btn" type="submit"><i class="fas fa-filter"></i>Filter</button>
        @if(request()->hasAny(['search','status']))<a class="filter-clear" href="{{ route('dashboard.comments') }}">Clear</a>@endif
    </form>
</section>

<section class="admin-card">
    <div class="admin-card-title"><i class="far fa-comments"></i> Moderation queue</div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Reader</th><th>Comment</th><th>Post</th><th>Date</th><th>Moderation</th><th></th></tr></thead>
            <tbody>
            @forelse($comments as $comment)
                <tr>
                    <td><div class="comment-reader"><span class="comment-reader-avatar">{{ strtoupper(substr($comment->user_name, 0, 1)) }}</span><div><b>{{ $comment->user_name }}</b><br><small>{{ $comment->user_email }}</small></div></div></td>
                    <td><div class="comment-copy">{{ Str::limit($comment->comment, 180) }}</div></td>
                    <td>@if($comment->post)<a class="table-link" href="{{ route('posts.show', $comment->post) }}" target="_blank">{{ Str::limit($comment->post->title, 38) }}</a>@else<span class="muted">Deleted post</span>@endif</td>
                    <td>{{ $comment->created_at?->format('M d, Y H:i') }}</td>
                    <td><form class="admin-actions" method="post" action="{{ route('dashboard.comments.update', $comment) }}">@csrf @method('patch')<select name="status" aria-label="Comment status">@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected($comment->status === $status)>{{ ucfirst($status) }}</option>@endforeach</select><button type="submit"><i class="fas fa-check"></i>Save</button></form></td>
                    <td><form method="post" action="{{ route('dashboard.comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment permanently?')">@csrf @method('delete')<button class="delete-icon-btn" type="submit" title="Delete comment"><i class="fas fa-trash"></i></button></form></td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-admin"><i class="far fa-comments"></i><br>No comments match your filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $comments->links() }}</div>
</section>
@endsection

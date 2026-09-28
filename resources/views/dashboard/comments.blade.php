@extends('layouts.admin')
@section('title', 'Comments')
@section('page-title', 'Comment Moderation')
@section('content')
<div class="admin-heading">
    <div><h1>Reader comments</h1><p>Review discussions and keep the community constructive.</p></div>
</div>
<section class="admin-card">
    <div class="admin-card-title"><i class="far fa-comments"></i> Moderation queue</div>
    <table class="admin-table">
        <thead><tr><th>Reader</th><th>Comment</th><th>Post</th><th>Moderation</th></tr></thead>
        <tbody>
        @forelse($comments as $comment)
            <tr>
                <td><div class="comment-reader"><span class="comment-reader-avatar">{{ strtoupper(substr($comment->user_name, 0, 1)) }}</span><div><b>{{ $comment->user_name }}</b><br><small>{{ $comment->user_email }}</small></div></div></td>
                <td><div class="comment-copy">{{ Str::limit($comment->comment, 150) }}</div></td>
                <td>{{ Str::limit($comment->post?->title ?? 'Deleted post', 42) }}</td>
                <td>
                    <form class="admin-actions" method="post" action="{{ route('dashboard.comments.update', $comment) }}">
                        @csrf @method('patch')
                        <select name="status" aria-label="Comment status"><option @selected($comment->status === 'pending')>pending</option><option @selected($comment->status === 'approved')>approved</option><option @selected($comment->status === 'rejected')>rejected</option></select>
                        <button type="submit" aria-label="Save comment status"><i class="fas fa-check"></i> Save</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty-admin"><i class="far fa-comments"></i><br>No comments are waiting for review.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $comments->links() }}</div>
</section>
@endsection

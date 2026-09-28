@extends('layouts.app')
@section('title', $post->title)
@section('content')
<article class="article">
    <div class="article-topline">
        <a href="{{ route('posts.index') }}"><i class="fas fa-arrow-left"></i> All stories</a>
        <span>{{ $post->category?->category_name ?? 'News' }}</span>
    </div>
    <header class="article-header">
        <h1>{{ $post->title }}</h1>
        <p class="article-deck">A focused read from the News Blog newsroom.</p>
        <div class="article-meta">
            <span class="article-avatar">{{ strtoupper(substr($post->author?->name ?? 'N', 0, 1)) }}</span>
            <div><b>{{ $post->author?->name ?? 'News Blog' }}</b><small>{{ optional($post->published_at ?? $post->created_at)->format('F d, Y') }} · {{ $post->views ?? 0 }} views</small></div>
        </div>
    </header>
    @if($post->image)
        <figure class="article-cover"><img src="{{ asset('images/'.$post->image) }}" alt="{{ $post->title }}"></figure>
    @endif
    <div class="article-content">{!! nl2br(e(strip_tags($post->content))) !!}</div>

    <section class="discussion-panel" id="discussion">
        <div class="discussion-heading">
            <div><span class="section-eyebrow">COMMUNITY</span><h2>Join the discussion</h2><p>Share a thoughtful response to this story.</p></div>
            <span class="comment-count"><i class="far fa-comment-dots"></i> {{ $comments->count() }} {{ Str::plural('comment', $comments->count()) }}</span>
        </div>

        <div class="comment-list">
            @forelse($comments as $comment)
                <article class="comment-card">
                    <span class="comment-avatar">{{ strtoupper(substr($comment->user_name, 0, 1)) }}</span>
                    <div class="comment-body"><div class="comment-author"><b>{{ $comment->user_name }}</b><span>{{ optional($comment->created_at)->diffForHumans() }}</span></div><p>{{ $comment->comment }}</p></div>
                </article>
            @empty
                <div class="comment-empty"><i class="far fa-comments"></i><div><b>Start the conversation</b><p>Be the first reader to share a perspective.</p></div></div>
            @endforelse
        </div>

        <form class="comment-form" method="post" action="{{ route('comments.store', (string) $post->_id) }}">
            @csrf
            <div class="comment-form-title"><i class="fas fa-pen-nib"></i><div><h3>Leave a comment</h3><p>Your email address will not be displayed publicly.</p></div></div>
            <div class="comment-fields">
                <label><span>Name</span><div class="input-wrap"><i class="far fa-user"></i><input name="user_name" value="{{ old('user_name') }}" placeholder="Your full name" required></div></label>
                <label><span>Email</span><div class="input-wrap"><i class="far fa-envelope"></i><input type="email" name="user_email" value="{{ old('user_email') }}" placeholder="you@example.com" required></div></label>
            </div>
            <label><span>Comment</span><textarea name="comment" placeholder="Share your thoughts respectfully..." required>{{ old('comment') }}</textarea></label>
            <div class="comment-submit-row"><small><i class="fas fa-shield-halved"></i> Comments are reviewed before publication.</small><button type="submit">Submit comment <i class="fas fa-arrow-right"></i></button></div>
        </form>
        @if(!empty($demoMode))<p class="discussion-note"><i class="fas fa-circle-info"></i> Comments are read-only until MongoDB is connected.</p>@endif
    </section>
</article>
@endsection

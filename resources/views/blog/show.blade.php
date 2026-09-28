@extends('layouts.app')
@section('title', $post->title)
@section('content')
@php
    $shareUrl = urlencode(url()->current());
    $shareTitle = urlencode($post->title);
@endphp
<article class="article article-wide">
    <div class="article-topline">
        <a href="{{ route('posts.index') }}"><i class="fas fa-arrow-left"></i> All stories</a>
        <span>{{ $post->category?->category_name ?? 'News' }}</span>
    </div>
    <header class="article-header">
        <h1>{{ $post->title }}</h1>
        <p class="article-deck">Reporting and perspective from the News Blog newsroom.</p>
        <div class="article-meta">
            <span class="article-avatar">{{ strtoupper(substr($post->author?->name ?? 'N', 0, 1)) }}</span>
            <div><b>{{ $post->author?->name ?? 'News Blog' }}</b><small>{{ optional($post->published_at ?? $post->created_at)->format('F d, Y') }} · {{ max(2, ceil(str_word_count(App\Support\ArticleText::clean($post->content)) / 200)) }} min read · {{ number_format($post->views ?? 0) }} views</small></div>
        </div>
    </header>

    @if($post->image)<figure class="article-cover"><img src="{{ App\Support\PostImage::url($post->image) }}" alt="{{ $post->title }}"></figure>@endif

    <div class="article-layout">
        <div>
            <div class="article-content">{!! nl2br(e(App\Support\ArticleText::clean($post->content))) !!}</div>

            <div class="article-after">
                <div class="article-tags"><a href="{{ route('home', ['category' => $post->category_id]) }}">#{{ Str::slug($post->category?->category_name ?? 'news') }}</a><a href="{{ route('posts.index') }}">#latest</a><a href="{{ route('posts.index', ['q' => 'trending']) }}">#trending</a></div>
                <div class="share-buttons" aria-label="Share this article">
                    <span>Share</span>
                    <a class="facebook" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" aria-label="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a class="twitter" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" aria-label="Share on X"><i class="fab fa-x-twitter"></i></a>
                    <a class="linkedin" target="_blank" rel="noopener" href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" aria-label="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a class="whatsapp" target="_blank" rel="noopener" href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" aria-label="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <section class="discussion-panel" id="discussion">
                <div class="discussion-heading"><div><span class="section-eyebrow">COMMUNITY</span><h2>Join the discussion</h2><p>Share a thoughtful response to this story.</p></div><span class="comment-count"><i class="far fa-comment-dots"></i> {{ $comments->count() }} {{ Str::plural('comment', $comments->count()) }}</span></div>
                <div class="comment-list">
                    @forelse($comments as $comment)
                        <article class="comment-card"><span class="comment-avatar">{{ strtoupper(substr($comment->user_name, 0, 1)) }}</span><div class="comment-body"><div class="comment-author"><b>{{ $comment->user_name }}</b><span>{{ optional($comment->created_at)->diffForHumans() }}</span></div><p>{{ $comment->comment }}</p></div></article>
                    @empty
                        <div class="comment-empty"><i class="far fa-comments"></i><div><b>Start the conversation</b><p>Be the first reader to share a perspective.</p></div></div>
                    @endforelse
                </div>
                <form class="comment-form" method="post" action="{{ route('comments.store', (string) $post->_id) }}">
                    @csrf
                    <div class="comment-form-title"><i class="fas fa-pen-nib"></i><div><h3>Leave a comment</h3><p>Your email address will not be displayed publicly.</p></div></div>
                    <div class="comment-fields"><label><span>Name</span><div class="input-wrap"><i class="far fa-user"></i><input name="user_name" value="{{ old('user_name') }}" placeholder="Your full name" required></div></label><label><span>Email</span><div class="input-wrap"><i class="far fa-envelope"></i><input type="email" name="user_email" value="{{ old('user_email') }}" placeholder="you@example.com" required></div></label></div>
                    <label><span>Comment</span><textarea name="comment" minlength="10" maxlength="2000" placeholder="Share your thoughts respectfully..." required>{{ old('comment') }}</textarea></label>
                    <div class="comment-submit-row"><small><i class="fas fa-shield-halved"></i> Comments are reviewed before publication.</small><button type="submit">Submit comment <i class="fas fa-arrow-right"></i></button></div>
                </form>
                @if(!empty($demoMode))<p class="discussion-note"><i class="fas fa-circle-info"></i> Comments are read-only until MongoDB is connected.</p>@endif
            </section>
        </div>

        <aside class="article-sidebar">
            <section class="author-panel">
                <span class="author-panel-avatar">{{ strtoupper(substr($post->author?->name ?? 'N', 0, 1)) }}</span>
                <span class="section-eyebrow">WRITTEN BY</span><h3>{{ $post->author?->name ?? 'News Blog' }}</h3>
                <p>Reporting with clarity, context and a commitment to independent journalism.</p>
                <div class="author-socials"><a href="#" aria-label="X"><i class="fab fa-x-twitter"></i></a><a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a></div>
            </section>

            <section class="related-panel">
                <div class="related-title"><i class="fas fa-link"></i><span>Related Stories</span></div>
                @forelse($related as $item)
                    <a class="related-story" href="{{ route('posts.show', $item->_id) }}">
                        <img src="{{ App\Support\PostImage::url($item->image) }}" alt="{{ $item->title }}">
                        <span><b>{{ Str::limit($item->title, 58) }}</b><small>{{ optional($item->published_at ?? $item->created_at)->diffForHumans() }}</small></span>
                    </a>
                @empty
                    <p class="related-empty">More stories in this category are coming soon.</p>
                @endforelse
            </section>

            <a class="article-newsletter" href="{{ route('home') }}#latest-news"><i class="fas fa-bolt"></i><span><b>Stay informed</b><small>Explore the latest newsroom updates</small></span><i class="fas fa-arrow-right"></i></a>
        </aside>
    </div>
</article>
@endsection

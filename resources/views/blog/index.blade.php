@extends('layouts.app')
@section('title', 'News Blog - Latest News & Updates')
@section('body-class', 'editorial-home')
@push('styles')<link rel="stylesheet" href="{{ asset('css/homepage.css') }}">@endpush
@section('content')
@php($featured = $posts->getCollection()->take(2))
@php($regular = $posts->getCollection()->skip(2))

<header class="news-hero" aria-labelledby="hero-title">
    <p class="hero-eyebrow">YOUR DAILY DOSE OF PERSPECTIVE</p>
    <h1 class="hero-display" id="hero-title"><span>NEWS THAT</span><span>MATTERS.</span></h1>
    <div class="hero-visual">
        <img src="{{ asset('images/original-background.jpg') }}" alt="A collage of Sri Lankan travel, culture, health and sports stories" fetchpriority="high">
        <span class="hero-image-caption"><span class="live-dot" aria-hidden="true"></span> A world of stories. One place.</span>
    </div>
    <div class="hero-intro glass-card">
        <span class="eyebrow">WELCOME TO NEWS BLOG</span>
        <p class="hero-intro-title">NEW STORIES.<br>FRESH IDEAS.<br>EVERY DAY.</p>
        <p class="hero-intro-copy">Your trusted source for news, in-depth analysis and perspectives from around the world.</p>
    </div>
    <div class="hero-stats" aria-label="Explore the newsroom">
        <a class="hero-stat glass-card" href="{{ route('posts.index') }}"><strong>{{ $posts->total() }}</strong><span>STORIES<br>TO EXPLORE</span><span class="stat-arrow" aria-hidden="true">↗</span></a>
        <a class="hero-stat glass-card glass-lime" href="#browse-categories"><strong>{{ $categories->count() }}</strong><span>NEWS<br>CATEGORIES</span><span class="stat-arrow" aria-hidden="true">↗</span></a>
        <a class="hero-stat glass-card glass-peach" href="#popular-news"><strong>{{ $popular->count() }}</strong><span>POPULAR<br>READS</span><span class="stat-arrow" aria-hidden="true">↗</span></a>
    </div>
    <div class="hero-actions">
        <div class="hero-text-links"><a href="#latest-news">LATEST NEWS</a><a href="{{ route('about') }}">ABOUT US</a></div>
        <a href="{{ $featured->isEmpty() ? '#latest-news' : '#featured-stories' }}" class="pill-button pill-dark">EXPLORE STORIES <span aria-hidden="true">↗</span></a>
    </div>
    <a class="hero-contact pill-button pill-orange" href="{{ route('contact') }}"><i class="far fa-envelope" aria-hidden="true"></i> LET'S TALK <span aria-hidden="true">↗</span></a>
</header>

<div class="news-shell">
    <section class="category-strip" id="browse-categories" aria-label="Browse news categories">
        <span class="eyebrow">FIND YOUR INTEREST</span>
        <div class="category-pills">
            <a href="{{ route('home') }}" class="category-pill {{ !request('category') ? 'selected' : '' }}">All stories <span aria-hidden="true">↗</span></a>
            @foreach($categories as $category)
                <a href="{{ route('home', ['category' => (string) $category->_id]) }}" class="category-pill {{ (string) request('category') === (string) $category->_id ? 'selected' : '' }}">{{ $category->category_name }} <span aria-hidden="true">↗</span></a>
            @endforeach
        </div>
    </section>

    @if(request()->filled('q') || request()->filled('category'))
        <div class="filter-notice glass-card" role="status">
            <span>{{ $posts->total() }} {{ Str::plural('story', $posts->total()) }} found{{ request()->filled('q') ? ' for “'.request('q').'”' : '' }}{{ request()->filled('category') ? ' in the selected category' : '' }}.</span>
            <a href="{{ route('home') }}">Clear filters <span aria-hidden="true">×</span></a>
        </div>
    @endif

    @if($featured->isNotEmpty())
        <section class="featured-section" id="featured-stories" aria-labelledby="featured-title">
            <div class="section-heading-row">
                <div><span class="eyebrow">THE EDITOR'S PICKS</span><h2 id="featured-title">Featured <span>stories.</span></h2></div>
                <a class="underlined-link" href="{{ route('posts.index') }}">ALL STORIES <span aria-hidden="true">↗</span></a>
            </div>
            <div class="featured-grid">
                @foreach($featured as $post)
                    <article class="story-card featured-card glass-card {{ $loop->even ? 'glass-lime' : '' }}">
                        <a class="story-image featured-image" href="{{ route('posts.show', $post->_id) }}" aria-label="Read {{ $post->title }}">
                            <img src="{{ App\Support\PostImage::url($post->image) }}" alt="{{ $post->title }}" loading="lazy">
                            <span class="category-tag">{{ $post->category?->category_name ?? 'News' }}</span>
                        </a>
                        <div class="story-copy">
                            <div class="post-meta"><span>{{ $post->author?->name ?? 'News Blog' }}</span><span>{{ $post->published_at?->diffForHumans() }}</span></div>
                            <h3><a href="{{ route('posts.show', $post->_id) }}">{{ $post->title }}</a></h3>
                            <p>{{ Str::limit(App\Support\ArticleText::clean($post->content), 145) }}</p>
                            <a class="story-read" href="{{ route('posts.show', $post->_id) }}">READ STORY <span class="round-arrow" aria-hidden="true">↗</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="content-wrapper" id="latest-news">
        <section class="latest-posts" aria-labelledby="latest-title">
            <div class="section-heading-row"><div><span class="eyebrow">STAY CURIOUS. STAY INFORMED.</span><h2 id="latest-title">The latest <span>news.</span></h2></div></div>
            <div class="posts-grid">
                @forelse($regular as $post)
                    <article class="story-card post-card glass-card {{ $loop->iteration % 3 === 0 ? 'glass-peach' : ($loop->even ? 'glass-lime' : '') }}">
                        <a class="story-image post-image" href="{{ route('posts.show', $post->_id) }}" aria-label="Read {{ $post->title }}">
                            <img src="{{ App\Support\PostImage::url($post->image) }}" alt="{{ $post->title }}" loading="lazy">
                            <span class="category-tag">{{ $post->category?->category_name ?? 'News' }}</span>
                        </a>
                        <div class="story-copy">
                            <div class="post-meta"><span>{{ $post->author?->name ?? 'News Blog' }}</span><span>{{ $post->published_at?->diffForHumans() }}</span></div>
                            <h3><a href="{{ route('posts.show', $post->_id) }}">{{ $post->title }}</a></h3>
                            <p>{{ Str::limit(App\Support\ArticleText::clean($post->content), 110) }}</p>
                            <div class="story-footer"><a class="story-read" href="{{ route('posts.show', $post->_id) }}">READ STORY <span class="round-arrow" aria-hidden="true">↗</span></a><span class="post-stats"><i class="far fa-eye" aria-hidden="true"></i> {{ $post->views ?? 0 }}</span></div>
                        </div>
                    </article>
                @empty
                    <div class="empty-stories glass-card"><span class="eyebrow">{{ $featured->isEmpty() ? 'NO MATCHES YET' : 'YOU ARE ALL CAUGHT UP' }}</span><h3>{{ $featured->isEmpty() ? 'No stories found.' : 'More perspectives await.' }}</h3><p>{{ $featured->isEmpty() ? 'Try another search or explore all our stories.' : 'Explore the full archive for more news and ideas.' }}</p><a class="pill-button pill-dark" href="{{ route('posts.index') }}">EXPLORE ALL STORIES <span aria-hidden="true">↗</span></a></div>
                @endforelse
            </div>
            @if($posts->hasPages())
                <nav class="home-pagination" aria-label="News pagination">
                    @if($posts->onFirstPage())<span class="page-disabled">← Previous</span>@else<a href="{{ $posts->previousPageUrl() }}">← Previous</a>@endif
                    <span>PAGE {{ $posts->currentPage() }} OF {{ $posts->lastPage() }}</span>
                    @if($posts->hasMorePages())<a href="{{ $posts->nextPageUrl() }}">Next →</a>@else<span class="page-disabled">Next →</span>@endif
                </nav>
            @endif
        </section>

        <aside class="sidebar" aria-label="More from News Blog">
            <section class="sidebar-widget glass-card" id="popular-news">
                <span class="eyebrow">WHAT'S GETTING ATTENTION</span><h2 class="widget-title">Popular reads<span class="heading-dot" aria-hidden="true">.</span></h2>
                @forelse($popular as $post)
                    <a class="popular-post" href="{{ route('posts.show', $post->_id) }}"><span class="rank">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span class="popular-content"><strong>{{ $post->title }}</strong><span class="popular-meta">{{ $post->category?->category_name }} · {{ $post->published_at?->diffForHumans() }}</span></span><span class="popular-arrow" aria-hidden="true">↗</span></a>
                @empty<p class="widget-empty">Popular stories will appear here.</p>@endforelse
            </section>
            <section class="sidebar-widget glass-card glass-lime">
                <span class="eyebrow">A LITTLE OF EVERYTHING</span><h2 class="widget-title">Categories<span class="heading-dot" aria-hidden="true">.</span></h2>
                @foreach($categories as $category)<a class="category-item" href="{{ route('home', ['category' => (string) $category->_id]) }}"><span>{{ $category->category_name }}</span><span aria-hidden="true">↗</span></a>@endforeach
            </section>
            <section class="sidebar-widget newsletter-widget glass-card glass-peach">
                <span class="newsletter-icon" aria-hidden="true"><i class="far fa-envelope"></i></span><span class="eyebrow">GOOD STORIES, STRAIGHT TO YOU</span><h2 class="widget-title">Stay in<br>the know<span class="heading-dot" aria-hidden="true">.</span></h2>
                <p>Fresh perspectives and daily updates, delivered to your inbox.</p>
                <form class="newsletter-form" method="post" action="{{ route('subscribe') }}">@csrf<label for="newsletter-email">Your email address</label><input id="newsletter-email" type="email" name="email" autocomplete="email" placeholder="you@example.com" required><button class="pill-button pill-dark" type="submit">SUBSCRIBE <span aria-hidden="true">↗</span></button></form>
            </section>
            <section class="sidebar-widget glass-card">
                <span class="eyebrow">JOIN THE CONVERSATION</span><h2 class="widget-title">Trending tags<span class="heading-dot" aria-hidden="true">.</span></h2>
                <div class="tags-cloud">@foreach(['Breaking News', 'Technology', 'Politics', 'Sports', 'Entertainment', 'Health', 'Business', 'World News'] as $tag)<a href="{{ route('home', ['q' => $tag]) }}" class="tag">{{ $tag }}</a>@endforeach</div>
            </section>
        </aside>
    </div>

    <section class="news-cta glass-card glass-peach" aria-labelledby="cta-title"><div><span class="eyebrow">LET'S CREATE SOMETHING GREAT</span><h2 id="cta-title">Your story.<br><span>Our audience.</span></h2><p>Share an idea or reach an engaged community through our newsroom.</p></div><a href="{{ route('contact') }}" class="pill-button pill-orange"><i class="far fa-envelope" aria-hidden="true"></i> LET'S TALK <span aria-hidden="true">↗</span></a></section>
</div>
@endsection

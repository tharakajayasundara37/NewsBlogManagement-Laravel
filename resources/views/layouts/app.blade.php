<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#262626">
    <title>@yield('title', 'News Blog')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pro.css') }}">
    <link rel="stylesheet" href="{{ asset('css/polish.css') }}">
    <link rel="stylesheet" href="{{ asset('css/editorial.css') }}">
    @stack('styles')
</head>
<body class="@yield('body-class')">
<nav class="modern-nav" aria-label="Primary navigation">
    <div class="nav-container">
        <a class="logo-wrapper" href="{{ route('home') }}" aria-label="News Blog home">
            <div class="logo-container"><span class="brand-diamond" aria-hidden="true">◆</span><span>News Blog</span></div>
        </a>
        <button class="mobile-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="primary-menu">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav-menu" id="primary-menu">
            <li><a class="nav-link {{ request()->routeIs('home') && !request('category') ? 'active' : '' }}" href="{{ route('home') }}"><i class="fas fa-house"></i><span>Home</span></a></li>
            <li class="nav-item">
                <div class="nav-category-control">
                    <a class="nav-link {{ request()->filled('category') || request()->routeIs('posts.index') ? 'active' : '' }}" href="{{ route('posts.index') }}"><i class="fas fa-layer-group"></i><span>Categories</span></a>
                    <button class="category-toggle" type="button" aria-label="Show categories" aria-expanded="false" aria-controls="nav-categories"><i class="fas fa-chevron-down" aria-hidden="true"></i></button>
                </div>
                <ul class="dropdown-menu" id="nav-categories">
                    <li><a class="dropdown-link" href="{{ route('posts.index') }}"><span>All Stories</span><i class="fas fa-arrow-right"></i></a></li>
                    @foreach(($categories ?? collect())->take(8) as $cat)
                        <li><a class="dropdown-link" href="{{ route('home', ['category' => (string) $cat->_id]) }}"><span>{{ $cat->category_name }}</span><i class="fas fa-arrow-right"></i></a></li>
                    @endforeach
                </ul>
            </li>
            <li><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}"><i class="fas fa-circle-info"></i><span>About</span></a></li>
            <li><a class="nav-link {{ request()->routeIs('contact*') ? 'active' : '' }}" href="{{ route('contact') }}"><i class="fas fa-paper-plane"></i><span>Contact</span></a></li>
            <li>
                <form class="nav-search" action="{{ route('home') }}" role="search">
                    <input type="search" name="q" aria-label="Search news" placeholder="Search stories..." value="{{ request('q') }}">
                    <button type="submit" aria-label="Search"><i class="fas fa-search"></i></button>
                </form>
            </li>
            <li class="nav-actions">
                @auth
                    <a class="login-btn" href="{{ route('dashboard') }}"><span>Dashboard</span><span aria-hidden="true">↗</span></a>
                @else
                    <a class="login-btn" href="{{ route('login') }}"><span>Login</span><span aria-hidden="true">↗</span></a>
                @endauth
            </li>
        </ul>
    </div>
</nav>

@if(session('success'))
    <div class="flash"><i class="fas fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif
@if($errors->any())
    <div class="flash error"><i class="fas fa-circle-exclamation"></i><span>{{ $errors->first() }}</span></div>
@endif

<main id="main-content">@yield('content')</main>

<footer class="main-footer">
    <div class="footer-grid">
        <section class="footer-about">
            <div class="footer-logo"><span aria-hidden="true">◆</span> News Blog</div>
            <p>Your trusted source for timely reporting, useful perspectives and stories that matter—every day.</p>
            <div class="socials">
                @foreach(['facebook-f', 'twitter', 'instagram', 'youtube', 'linkedin-in'] as $icon)
                    <a href="#" aria-label="{{ $icon }}"><i class="fab fa-{{ $icon }}"></i></a>
                @endforeach
            </div>
        </section>
        <section><h3>Quick Links</h3><ul><li><a href="{{ route('home') }}">Home</a></li><li><a href="{{ route('posts.index') }}">All Stories</a></li><li><a href="{{ route('about') }}">About Us</a></li><li><a href="{{ route('contact') }}">Contact Us</a></li></ul></section>
        <section><h3>Top Categories</h3><ul>@foreach(($categories ?? collect())->take(6) as $cat)<li><a href="{{ route('home', ['category' => (string) $cat->_id]) }}">{{ $cat->category_name }}</a></li>@endforeach</ul></section>
        <section class="footer-contact"><h3>Contact Info</h3><ul><li><i class="fas fa-location-dot"></i><span>2/83, Kirimetiyawe,<br>Ambanpola, Sri Lanka</span></li><li><i class="fas fa-phone"></i><span>+94 74 315 3951</span></li><li><i class="fas fa-envelope"></i><span>newsblog@gmail.com</span></li></ul></section>
    </div>
    <div class="footer-bottom"><span>© {{ date('Y') }} News Blog. All rights reserved.</span><span>Independent stories. Clear perspectives.</span></div>
</footer>
<button class="back-top" type="button" aria-label="Back to top" onclick="scrollTo({top:0, behavior:'smooth'})"><i class="fas fa-arrow-up"></i></button>
<script src="{{ asset('js/navigation.js') }}" defer></script>
@stack('scripts')
</body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#071f2d">
    <title>@yield('title', 'News Blog')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pro.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/polish.css') }}">
    <link rel="stylesheet" href="{{ asset('css/editorial.css') }}">
</head>
<body>
<nav class="modern-nav" aria-label="Primary navigation">
    <div class="nav-container">
        <a class="logo-wrapper" href="{{ route('home') }}" aria-label="News Blog home">
            <div class="logo-container"><span>News</span><i class="logo-divider"></i><span>Blog</span></div>
            <div class="logo-tagline">YOUR DAILY NEWS SOURCE</div>
        </a>
        <button class="mobile-toggle" type="button" aria-label="Toggle navigation" onclick="document.querySelector('.nav-menu').classList.toggle('open')">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav-menu">
            <li><a class="nav-link {{ request()->routeIs('home') && !request('category') ? 'active' : '' }}" href="{{ route('home') }}"><i class="fas fa-house"></i><span>Home</span></a></li>
            <li class="nav-item">
                <a class="nav-link {{ request()->filled('category') ? 'active' : '' }}" href="{{ route('posts.index') }}"><i class="fas fa-layer-group"></i><span>Categories</span><i class="fas fa-chevron-down nav-chevron"></i></a>
                <ul class="dropdown-menu">
                    @foreach(($categories ?? collect())->take(8) as $cat)
                        <li><a class="dropdown-link" href="{{ route('home', ['category' => (string) $cat->_id]) }}"><span>{{ $cat->category_name }}</span><i class="fas fa-arrow-right"></i></a></li>
                    @endforeach
                </ul>
            </li>
            <li><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}"><i class="fas fa-circle-info"></i><span>About</span></a></li>
            <li><a class="nav-link {{ request()->routeIs('contact*') ? 'active' : '' }}" href="{{ route('contact') }}"><i class="fas fa-paper-plane"></i><span>Contact</span></a></li>
            <li>
                <form class="nav-search" action="{{ route('home') }}" role="search">
                    <input name="q" aria-label="Search news" placeholder="Search stories..." value="{{ request('q') }}">
                    <button type="submit" aria-label="Search"><i class="fas fa-search"></i></button>
                </form>
            </li>
            <li class="nav-actions">
                @auth
                    <a class="login-btn" href="{{ route('dashboard') }}"><i class="fas fa-border-all"></i><span>Dashboard</span></a>
                @else
                    <a class="login-btn" href="{{ route('login') }}"><i class="fas fa-arrow-right-to-bracket"></i><span>Login</span></a>
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

<main>@yield('content')</main>

<footer class="main-footer">
    <div class="footer-grid">
        <section class="footer-about">
            <div class="footer-logo">News <span>|</span> Blog</div>
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
@stack('scripts')
</body>
</html>

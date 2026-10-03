@extends('layouts.app')
@section('title', 'About Us - News Blog')
@section('body-class', 'travel-home glass-page')
@push('page-styles')<link rel="stylesheet" href="{{ asset('css/travel-home.css') }}">@endpush
@section('content')
<section class="page-banner about-banner"><div><span>INDEPENDENT JOURNALISM</span><h1>About News Blog</h1><p>Clarity, context and stories that help readers understand a changing world.</p></div></section>

<div class="about-shell">
    <section class="about-mission">
        <div><span class="section-eyebrow">OUR MISSION</span><h2>Information has the power to transform lives.</h2><p>Our mission is to deliver accurate, timely and unbiased news coverage that keeps readers informed and empowered.</p><p>In an era of information overload, our journalists verify facts, provide context and present the stories that matter most to our audience. We place public interest, transparency and editorial independence at the centre of every decision.</p><div class="mission-signature"><i class="fas fa-quote-left"></i><span>Truth first. Readers always.</span></div></div>
        <div class="mission-visual"><img src="{{ asset('images/posts/1765458001_image1170x530cropped.webp') }}" alt="News and technology"><span><b>Always curious.</b> Always accountable.</span></div>
    </section>

    <section class="about-stats" aria-label="News Blog statistics">
        @foreach([['5+','Years of service'],['10K+','Articles published'],['2M+','Monthly readers'],['50+','Award wins']] as $stat)
            <div><b>{{ $stat[0] }}</b><span>{{ $stat[1] }}</span></div>
        @endforeach
    </section>

    <section class="leadership-section">
        <div class="section-intro"><span class="section-eyebrow">OUR PEOPLE</span><h2>Meet our leadership</h2><p>A multidisciplinary team shaping reliable reporting for every platform.</p></div>
        <div class="leadership-grid">
            <article class="leader-card featured-leader"><div class="leader-image"><img src="{{ asset('images/team/tharaka.jpg') }}" alt="Tharaka Jayasundara"></div><div class="leader-copy"><span>ADMIN-IN-CHIEF</span><h3>Tharaka Jayasundara</h3><p>Tharaka leads the editorial team with a focus on accuracy, audience trust and purposeful digital storytelling.</p><div class="leader-social"><a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a><a href="#" aria-label="X"><i class="fab fa-x-twitter"></i></a></div></div></article>
            <article class="leader-card"><div class="leader-placeholder amber">DA</div><div class="leader-copy"><span>NEWS DIRECTOR</span><h3>Dulanjan Anushika</h3><p>Dulanjan oversees newsroom operations and ensures breaking stories reach readers quickly and responsibly.</p></div></article>
            <article class="leader-card"><div class="leader-placeholder blue">AE</div><div class="leader-copy"><span>DIGITAL EDITOR</span><h3>Aruna Ekanayaka</h3><p>Aruna leads digital publishing and develops engaging ways to bring journalism to audiences across platforms.</p></div></article>
        </div>
    </section>

    <section class="values-section-pro">
        <div class="section-intro"><span class="section-eyebrow">WHAT GUIDES US</span><h2>Our core values</h2></div>
        <div class="values-grid-pro">
            @foreach([['bullseye','Accuracy','We verify every fact, cross-check sources and correct mistakes transparently.'],['scale-balanced','Integrity','We maintain high ethical standards and protect editorial independence.'],['users','Community','We listen to our readers and cover issues that shape their daily lives.'],['lightbulb','Curiosity','We ask better questions and search beyond the obvious for meaningful answers.']] as $value)
                <article><span><i class="fas fa-{{ $value[0] }}"></i></span><h3>{{ $value[1] }}</h3><p>{{ $value[2] }}</p></article>
            @endforeach
        </div>
    </section>

    <section class="about-cta"><span class="section-eyebrow">STAY INFORMED</span><h2>Join readers who choose clarity.</h2><p>Explore our latest reporting or speak with the team behind News Blog.</p><div><a class="cta-primary" href="{{ route('posts.index') }}"><i class="fas fa-newspaper"></i> Read Latest News</a><a class="cta-secondary" href="{{ route('contact') }}"><i class="fas fa-envelope"></i> Contact Us</a></div></section>
</div>
@endsection

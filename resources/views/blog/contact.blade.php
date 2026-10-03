@extends('layouts.app')
@section('title', 'Contact Us - News Blog')
@section('body-class', 'travel-home glass-page')
@push('page-styles')<link rel="stylesheet" href="{{ asset('css/travel-home.css') }}">@endpush
@section('content')
<section class="page-banner contact-banner"><div><span>LET'S TALK</span><h1>Contact Us</h1><p>Send a story tip, ask a question or start a conversation with our newsroom.</p></div></section>

<div class="contact-shell">
    <section class="contact-pro-grid">
        <div class="contact-details">
            <span class="section-eyebrow">GET IN TOUCH</span><h2>We would love to hear from you.</h2><p>Our team normally responds within two working days. For urgent newsroom matters, call our hotline.</p>
            <div class="contact-detail-list">
                <article><span><i class="fas fa-location-dot"></i></span><div><h3>Visit our newsroom</h3><p>2/83, Kirimetiyawe<br>Ambanpola, Sri Lanka</p><a href="https://maps.google.com/?q=Ambanpola%20Sri%20Lanka" target="_blank" rel="noopener">Open in maps <i class="fas fa-arrow-up-right-from-square"></i></a></div></article>
                <article><span><i class="fas fa-phone"></i></span><div><h3>Call the team</h3><p><a href="tel:+94743153951">+94 74 315 3951</a><br><a href="tel:+94712840392">+94 71 284 0392</a></p></div></article>
                <article><span><i class="fas fa-envelope"></i></span><div><h3>Email us</h3><p><a href="mailto:newsblog@gmail.com">newsblog@gmail.com</a><br><a href="mailto:corrections@newsblog.com">corrections@newsblog.com</a></p></div></article>
            </div>
            <div class="contact-social"><span>Follow the newsroom</span><div>@foreach(['facebook-f','x-twitter','instagram','linkedin-in'] as $icon)<a href="#" aria-label="{{ $icon }}"><i class="fab fa-{{ $icon }}"></i></a>@endforeach</div></div>
        </div>

        <form class="contact-form contact-form-pro" method="post" action="{{ route('contact.store') }}">
            @csrf
            <span class="section-eyebrow">SEND A MESSAGE</span><h2>Send us a Message</h2><p>Complete the form and the right member of our team will get back to you.</p>
            <div class="contact-field-grid"><label><span>Full Name *</span><div><i class="far fa-user"></i><input name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Your full name"></div></label><label><span>Email Address *</span><div><i class="far fa-envelope"></i><input type="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com"></div></label></div>
            <label><span>Subject *</span><div><i class="far fa-bookmark"></i><input name="subject" value="{{ old('subject') }}" required maxlength="200" placeholder="What is this about?"></div></label>
            <label><span>Message *</span><textarea name="message" required maxlength="3000" placeholder="Tell us how we can help...">{{ old('message') }}</textarea></label>
            <button class="form-submit" type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
            <small><i class="fas fa-shield-halved"></i> Your contact details are used only to respond to this enquiry.</small>
        </form>
    </section>

    <section class="faq-section-pro">
        <div class="section-intro"><span class="section-eyebrow">HELP CENTRE</span><h2>Frequently asked questions</h2><p>Quick answers about contacting and working with News Blog.</p></div>
        <div class="faq-list">
            @foreach([
                ['How long does it take to get a response?', 'We typically respond within 24–48 hours during business days. For urgent matters, please call our hotline.'],
                ['Can I submit a news tip anonymously?', 'Yes. You may leave identifying details out of your message. We respect privacy and protect confidential sources.'],
                ['How do I report an error in an article?', 'Email corrections@newsblog.com with the article link and details. Our editorial team will review it promptly.'],
                ['Do you offer advertising opportunities?', 'Yes. Use the contact form with “Advertising” in the subject and our partnerships team will share current options.']
            ] as $faq)
                <details><summary><span>{{ $faq[0] }}</span><i class="fas fa-plus"></i></summary><p>{{ $faq[1] }}</p></details>
            @endforeach
        </div>
    </section>
</div>
@endsection

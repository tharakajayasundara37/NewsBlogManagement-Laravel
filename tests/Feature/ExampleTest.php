<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_public_pages_render_and_dashboard_is_protected(): void
    {
        $this->get('/')->assertOk()->assertSee('WELCOME TO NEWS BLOG')->assertSee('Visit Sri Lanka');
        $this->get('/posts/36')->assertOk()->assertSee('Visit Sri Lanka')->assertSee('Related Stories')->assertSee('Share');
        $this->get('/about')->assertOk()->assertSee('About News Blog')->assertSee('Tharaka Jayasundara')->assertSee('Our core values');
        $this->get('/contact')->assertOk()->assertSee('Send us a Message')->assertSee('Frequently asked questions');
        $this->get('/login')->assertOk()->assertSee('Welcome back');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/dashboard/posts')->assertRedirect('/login');
        $this->get('/dashboard/categories')->assertRedirect('/login');
    }
}

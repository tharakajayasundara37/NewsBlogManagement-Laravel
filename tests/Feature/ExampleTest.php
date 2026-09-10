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
        $this->get('/')->assertOk()->assertSee('Welcome to News Blog')->assertSee('Visit Sri Lanka');
        $this->get('/posts/36')->assertOk()->assertSee('Visit Sri Lanka');
        $this->get('/about')->assertOk()->assertSee('About News Blog');
        $this->get('/contact')->assertOk()->assertSee('Send us a Message');
        $this->get('/login')->assertOk()->assertSee('Welcome back');
        $this->get('/dashboard')->assertRedirect('/login');
    }
}

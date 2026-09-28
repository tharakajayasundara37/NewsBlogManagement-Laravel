<?php

namespace Tests\Unit;

use App\Support\PostImage;
use Tests\TestCase;

class PostImageTest extends TestCase
{
    public function test_it_preserves_embedded_and_remote_images(): void
    {
        $embedded = 'data:image/png;base64,AAAA';

        $this->assertSame($embedded, PostImage::url($embedded));
        $this->assertSame('https://example.com/photo.jpg', PostImage::url('https://example.com/photo.jpg'));
        $this->assertTrue(PostImage::isEmbedded($embedded));
        $this->assertFalse(PostImage::isEmbedded('posts/photo.jpg'));
    }

    public function test_it_builds_public_urls_for_legacy_paths(): void
    {
        $this->assertSame('http://localhost/images/posts/photo.jpg', PostImage::url('posts/photo.jpg'));
        $this->assertSame('http://localhost/images/brand.jpg', PostImage::url(null));
    }
}

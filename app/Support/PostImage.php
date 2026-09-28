<?php

namespace App\Support;

class PostImage
{
    public static function url(?string $image): string
    {
        if (! $image) {
            return asset('images/brand.jpg');
        }
        if (str_starts_with($image, 'data:') || str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        return asset('images/'.ltrim($image, '/'));
    }

    public static function isEmbedded(?string $image): bool
    {
        return (bool) $image && str_starts_with($image, 'data:');
    }
}

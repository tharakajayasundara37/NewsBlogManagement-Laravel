<?php

namespace App\Support;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

class DatabaseBootstrap
{
    private static bool $checked = false;

    public static function run(): void
    {
        if (self::$checked || ! self::configured()) return;
        self::$checked = true;

        if (! User::exists()) {
            app(DatabaseSeeder::class)->run();
        }
    }

    public static function configured(): bool
    {
        $uri = (string) config('database.connections.mongodb.dsn', '');

        return config('database.default') === 'mongodb'
            && $uri !== ''
            && ! str_contains($uri, '127.0.0.1')
            && ! str_contains($uri, 'localhost');
    }
}

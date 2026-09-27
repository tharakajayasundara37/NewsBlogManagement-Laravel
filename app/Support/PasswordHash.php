<?php

namespace App\Support;

final class PasswordHash
{
    private const ALGORITHM = 'sha256';
    private const ITERATIONS = 210000;
    private const LENGTH = 64;

    public static function make(string $password): string
    {
        $salt = bin2hex(random_bytes(16));
        $digest = hash_pbkdf2(self::ALGORITHM, $password, $salt, self::ITERATIONS, self::LENGTH);

        return sprintf('pbkdf2-sha256$%d$%s$%s', self::ITERATIONS, $salt, $digest);
    }

    public static function check(string $password, string $hash): bool
    {
        $parts = explode('$', $hash, 4);

        if (count($parts) === 4 && $parts[0] === 'pbkdf2-sha256' && ctype_digit($parts[1])) {
            $iterations = (int) $parts[1];
            if ($iterations < 100000 || $iterations > 1000000) return false;

            $digest = hash_pbkdf2(self::ALGORITHM, $password, $parts[2], $iterations, strlen($parts[3]));

            return hash_equals($parts[3], $digest);
        }

        try {
            return password_verify($password, $hash);
        } catch (\Throwable) {
            return false;
        }
    }
}

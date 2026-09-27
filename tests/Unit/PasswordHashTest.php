<?php

namespace Tests\Unit;

use App\Support\PasswordHash;
use PHPUnit\Framework\TestCase;

class PasswordHashTest extends TestCase
{
    public function test_it_hashes_and_verifies_passwords_without_bcrypt(): void
    {
        $hash = PasswordHash::make('Admin@2026!News');

        $this->assertStringStartsWith('pbkdf2-sha256$', $hash);
        $this->assertTrue(PasswordHash::check('Admin@2026!News', $hash));
        $this->assertFalse(PasswordHash::check('wrong-password', $hash));
    }
}

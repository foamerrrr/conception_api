<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_pro_true(): void
    {
        $user = new User(['email' => 'john@entreprise.com']);

        $this->assertTrue($user->usesProfessionalEmail());
    }

    public function test_pro_false(): void
    {
        $user = new User(['email' => 'john@gmail.com']);

        $this->assertFalse($user->usesProfessionalEmail());
    }
}

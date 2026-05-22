<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_uses_professional_email_returns_true_for_entreprise_domain(): void
    {
        $user = new User(['email' => 'john@entreprise.com']);

        $this->assertTrue($user->usesProfessionalEmail());
    }

    public function test_uses_professional_email_returns_false_for_gmail(): void
    {
        $user = new User(['email' => 'john@gmail.com']);

        $this->assertFalse($user->usesProfessionalEmail());
    }
}

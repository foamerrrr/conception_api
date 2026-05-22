<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCreationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return [
            'title' => '1984',
            'author' => 'George Orwell',
            'summary' => 'Roman dystopique décrivant une société totalitaire contrôlée par Big Brother.',
            'isbn' => '9780451524935',
        ];
    }

    public function test_valid(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/books', $this->payload());

        $response->assertCreated();
        $this->assertDatabaseHas('books', ['isbn' => '9780451524935']);
    }

    public function test_invalid(): void
    {
        $user = User::factory()->create();
        $data = $this->payload();
        $data['title'] = 'AB';

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/books', $data);

        $response->assertStatus(422);
        $this->assertDatabaseCount('books', 0);
    }

    public function test_guest(): void
    {
        $response = $this->postJson('/api/v1/books', $this->payload());

        $response->assertUnauthorized();
        $this->assertDatabaseCount('books', 0);
    }
}

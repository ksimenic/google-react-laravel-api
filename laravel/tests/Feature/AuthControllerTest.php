<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Shared\MockSocialite;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use MockSocialite, RefreshDatabase;

    /** @test */
    public function it_redirects_to_google()
    {
        $this
            ->get('/api/auth')
            ->assertStatus(200)
            ->assertSee('accounts.google.com\/o\/oauth2\/auth');
    }

    /** @test */
    public function it_creates_new_user_on_first_authentication()
    {
        $this->mockSocialite(123, 'test@test.com', 'Test');

        $this
            ->get('/api/auth/callback')
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id', 'email', 'email_verified_at', 'name', 'created_at', 'updated_at'
                ],
                'access_token',
                'token_type',
            ]);

        $this->assertDatabaseCount(User::class, 1);
        /** @var User $user */
        $user = User::query()->first();

        $this->assertSame('123', $user->google_id);
        $this->assertSame('Test', $user->name);
        $this->assertSame('test@test.com', $user->email);
    }

    /** @test */
    public function it_gets_existing_user_on_non_first_authentication()
    {
        User::factory()->create([
            'google_id' => '123',
            'email' => 'test@test.com',
            'name' => 'Test',
        ]);

        $this->mockSocialite(123, 'test@test.com', 'Test');

        $this
            ->get('/api/auth/callback')
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id', 'email', 'email_verified_at', 'name', 'created_at', 'updated_at'
                ],
                'access_token',
                'token_type',
            ]);

        $this->assertDatabaseCount(User::class, 1);
    }

    /** @test */
    public function it_throws_exception_when_user_not_authenticated()
    {
        $this->mockSocialiteException();

        $this
            ->get('/api/auth/callback')
            ->assertStatus(422)
            ->assertJson([
                'error' => 'Invalid credentials provided.',
            ]);

        $this->assertDatabaseCount(User::class, 0);
    }
}

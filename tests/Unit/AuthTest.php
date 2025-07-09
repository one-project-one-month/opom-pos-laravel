<?php
namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_tokens()
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'access_token_expires_at',
                'refresh_token',
                'refresh_token_expires_at',
                'token_type',
            ]);
    }

    public function test_user_can_refresh_tokens()
    {
        $user          = User::factory()->create();
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $refreshToken = $loginResponse->json('refresh_token');

        $refreshResponse = $this->withHeader('Authorization', 'Bearer ' . $refreshToken)
            ->postJson('/api/v1/auth/refresh');

        $refreshResponse->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'access_token_expires_at',
                'refresh_token',
                'refresh_token_expires_at',
                'token_type',
            ]);
    }

    public function test_user_can_logout()
    {
        $user          = User::factory()->create();
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $accessToken = $loginResponse->json('access_token');

        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $accessToken)
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson(['message' => 'Logged out']);
    }
}

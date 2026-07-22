<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('user can register test', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'example',
        'email' => 'user@example.com',
        'password' => 'example123',
        'password_confirmation' => 'example123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'token'
            ],
            'errors',
            'meta'
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'user@example.com'
    ]);
});

test('requires validation field for register test', function () {
    $response = $this->postJson('/api/auth/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('cannot register with existing email test', function () {
    User::factory()->create(['email' => 'user@example.com']);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'example',
        'email' => 'user@example.com',
        'password' => 'example123',
        'password_confirmation' => 'example123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('cannot register with password less than 8 characters test', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'example',
        'email' => 'user@example.com',
        'password' => '1234567',
        'password_confirmation' => '1234567',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('cannot register with unconfirmed password test', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'example',
        'email' => 'user@example.com',
        'password' => 'example123',
        'password_confirmation' => 'different123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('login on existing user test', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
        'is_active' => true,
    ]);

    expect($user)->not()->toBeNull();

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'token'
            ],
            'errors',
            'meta'
        ]);
});

test('login fails with wrong password test', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
        'is_active' => true,
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson(['success' => false]);
});

test('login rate limiting test', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
        'is_active' => true,
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);
    }

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(429);
});

test('cannot access me endpoint without token test', function () {
    $response = $this->getJson('/api/auth/me');

    $response->assertStatus(401);
});

test('can access me endpoint with valid token test', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/auth/me');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'email'
            ],
            'errors',
            'meta'
        ])
        ->assertJson([
            'success' => true,
            'data' => [
                'email' => $user->email,
            ]
        ]);
});

test('user can logout and invalidate token test', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/auth/logout');

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->app['auth']->forgetGuards();

    $responseMe = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/auth/me');

    $responseMe->assertStatus(401);
});

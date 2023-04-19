<?php

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->endpoint = '/api/v1/user';
    $this->password = 'password123';
    $this->user = User::factory()->create(['email' => 'test@example.com', 'password' => $this->password]);
});

test('login with valid credentials returns a JWT token', function () {
    $response = $this->postJson($this->endpoint.'/login', [
        'email' => $this->user->email,
        'password' => $this->password,
    ]);

    $response
        ->assertStatus(200)
        ->assertJsonStructure(['token']);
});

test('login with invalid credentials returns a 401 error', function () {
    $response = $this->postJson($this->endpoint.'/login', [
        'email' => $this->user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401);
});

test('logout invalidates the JWT token', function () {
    $jti = uniqid('', true);
    $token = JWT::encode([
        'sub' => $this->user->id,
        'iat' => time(),
        'exp' => time() + (60 * config('jwt.expiration')),
        'jti' => $jti
    ], config('jwt.key'), config('jwt.algo'));

    $this->getJson($this->endpoint, [
        'Authorization' => 'Bearer ' . $token,
    ])->assertJsonStructure(['id']);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->getJson($this->endpoint.'/logout');

    $response
        ->assertStatus(200)
        ->assertJson(['message' => 'Successfully logged out']);

    $this->refreshApplication();

    // Try to use the invalidated token
    $response = $this->getJson($this->endpoint, [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertStatus(401);
});

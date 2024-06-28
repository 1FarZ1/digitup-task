

<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function Pest\Laravel\postJson;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
it('can register a user', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'imad@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', ['email' => 'imad@example.com']);
});

it('can login a user', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['token']);
});


it('cannot login a user with invalid password', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'invalid-password',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});

it('cannot register a user with an existing email', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => $user->email,
        'password' => 'password',

    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});
it('cannot register a user with missing data', function () {
    $response = postJson('/api/auth/register', [
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors('name');

    $response = postJson('/api/auth/register', [
        'name' => 'John Doe',
        'password' => 'password',
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors('email');

    $response = postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors('password');
});
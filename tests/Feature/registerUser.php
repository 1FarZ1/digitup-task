<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
it('can register a new user', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password',
    ];

    $response = $this->postJson('/api/auth/register', $userData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);
});

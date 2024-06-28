<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
it('can authenticate a user', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $credentials = [
        'email' => $user->email,
        'password' => 'password',
    ];

    $response = $this->postJson('/api/auth/login', $credentials);

    $response->assertStatus(200);
    $this->assertAuthenticated();
});




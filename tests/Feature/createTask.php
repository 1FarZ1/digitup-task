<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

it('can create a task', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $taskData = [
        'title' => 'New Task',
        'description' => 'This is a new task',
        'due_date' => '12/02/2025',
    ];

    // Send a POST request to create a task
    $response = $this->postJson('/api/tasks', $taskData);

    // Assert response status and structure
    $response->assertStatus(201)
             ->assertJsonStructure([
                 'id',
                 'title',
                 'description',
                 'due_date',
                 'created_at',
                 'updated_at',
             ]);

    $response->assertJson([
        'title' => 'New Task',
        'description' => 'This is a new task',
    ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'New Task',
    ]);
});
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




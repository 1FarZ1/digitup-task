<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;


uses(TestCase::class);
uses(RefreshDatabase::class);
it('it creates a new task for an authenticated user', function () {
    Sanctum::actingAs(User::factory()->create());

    $taskData = Task::factory()->raw();

    $response = $this->postJson('/api/tasks', $taskData);

    $response->assertStatus(201)
        ->assertJsonFragment(['title' => $taskData['title']]);
});

it('it updates a task owned by the user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $task = Task::factory()->create(['user_id' => $user->id]);
    $updatedTitle = 'Updated Title';

    $response = $this->putJson("/api/tasks/{$task->id}", ['title' => $updatedTitle]);

    $response->assertStatus(200)
        ->assertJsonFragment(['title' => $updatedTitle]);
});

it('it soft deletes a task owned by the user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);


});
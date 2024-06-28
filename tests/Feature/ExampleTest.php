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
        'due_date' => '07/07/2025',
    ];

    $response = $this->postJson('/api/tasks', $taskData);

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


it('unauthenticated user cannot create a task', function () {
    $taskData = [
        'title' => 'New Task',
        'description' => 'This is a new task',
        'due_date' => '07/07/2025',
    ];

    $response = $this->postJson('/api/tasks', $taskData);

    $response->assertStatus(401);
});

it('admin can fetch all tasks', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200);
});

it('user can fetch own tasks', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200);
});

it('unauthenticated user cannot fetch tasks', function () {
    $response = $this->getJson('/api/tasks');

    $response->assertStatus(401);
});

it('admin can fetch a task', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $task = Task::factory()->create();

    $response = $this->getJson('/api/tasks/' . $task->id);

    $response->assertStatus(200);
});

it('user can fetch own task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/tasks/' . $task->id);

    $response->assertStatus(200);
});

it('user cannot fetch another user\'s task', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $this->actingAs($user1);

    $task = Task::factory()->create(['user_id' => $user2->id]);

    $response = $this->getJson('/api/tasks/' . $task->id);

    $response->assertStatus(403);
});

it('admin can fetch a deleted task', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $task = Task::factory()->create();
    $task->delete();

    $response = $this->getJson('/api/tasks/' . $task->id);

    $response->assertStatus(200);
});

it('user cannot fetch a deleted task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $task = Task::factory()->create();
    $task->delete();
    $response = $this->getJson('
    /api/tasks/' . $task->id);
    $response->assertStatus(404);
});


// admin can get all deleted tasks
it('admin can get all deleted tasks', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $task = Task::factory()->create();
    $task->delete();

    $response = $this->getJson('/api/tasks/deleted');

    $response->assertStatus(200);
});

// user can update own task
// it('user can update own task', function () {
//     $user = User::factory()->create();
//     $this->actingAs($user);

//     $task = Task::factory()->create(['user_id' => $user->id]);

//     $taskData = [
//         'title' => 'Updated Task',
//         'description' => 'This is an updated task',
//         'due_date' => '07/07/2025',
//     ];

//     $response = $this->putJson('/api/tasks/' . $task->id, $taskData);

//     $response->assertStatus(200)
//              ->assertJson([
//                  'title' => 'Updated Task',
//                  'description' => 'This is an updated task',
//              ]);

//     $this->assertDatabaseHas('tasks', [
//         'title' => 'Updated Task',
//     ]);
// });


// // admin can update any task
// it('admin can update any task', function () {
//     $admin = User::factory()->create(['role' => 'admin']);
//     $this->actingAs($admin);

//     $task = Task::factory()->create();

//     $taskData = [
//         'title' => 'Updated Task',
//         'description' => 'This is an updated task',
//         'due_date' => '07/07/2025',
//     ];

//     $response = $this->putJson('/api/tasks/' . $task->id, $taskData);

//     $response->assertStatus(200)
//              ->assertJson([
//                  'title' => 'Updated Task',
//                  'description' => 'This is an updated task',
//              ]);

//     $this->assertDatabaseHas('tasks', [
//         'title' => 'Updated Task',
//     ]);
// });
<?php

it('registers a new user', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure(['access_token']);
});

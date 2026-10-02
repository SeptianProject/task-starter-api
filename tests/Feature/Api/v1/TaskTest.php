<?php

namespace Tests\Feature\Api\v1;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_index(): void
    {
        // arrange
        $tasks = Task::factory()->count(3)->create();
        // act
        $response = $this->get('/api/v1/tasks');
        // assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'kode',
                    'nama',
                    'selesai',
                ],
            ],
        ]);
    }

    public function test_simpan_task(): void
    {
        // arrange

        // act
        $response = $this->postJson('/api/v1/tasks', [
            'name' => 'Test Task',
            'is_completed' => false,
        ]);

        // assert
        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'kode',
                'nama',
                'selesai',
            ],
        ]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Test Task',
            'is_completed' => false,
        ]);
    }

    public function test_ubah_task(): void
    {
        // arrange
        $task = Task::factory()->create();

        // act
        $response = $this->putJson("/api/v1/tasks/{$task->id}", [
            'name' => 'Updated Task',
            'is_completed' => true,
        ]);

        // assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'kode',
                'nama',
                'selesai',
            ],
        ]);

        $response->assertJsonFragment(
            [
                'kode' => $task->id,
                'nama' => 'Updated Task',
                'selesai' => true,
            ]
        );

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task',
            'is_completed' => true,
        ]);
    }

    public function test_hapus_task(): void
    {
        // arrange
        $task = Task::factory()->create();

        // act
        $response = $this->deleteJson('/api/v1/tasks/' . $task->id);

        // assert
        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}

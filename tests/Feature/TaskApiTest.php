<?php

namespace Tests\Feature;

use App\Http\Resources\Api\TaskResource;
use App\Models\Api\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private $example_task = [
        'title' => 'Task 1',
        'description' => 'Task 1 description',
        'due_date' => '2025-01-20T15:00:00',
        'create_date' => '2025-01-20T15:00:00',
        'priority' => 'низкий',
        'category' => 'Work',
        'status' => 'не выполнена',
    ];

    public function test_index_positive_test(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/api/tasks');
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll(['data', 'links', 'meta'])->where('data', [])
        );

        $tasks[0] = $this->example_task;
        $tasks[1] = $this->example_task;
        $tasks[1]['create_date'] = now()->format('Y-m-d H:i:s');
        Task::create($tasks[0]);
        Task::create($tasks[1]);
        $tasks[0]['id'] = 1;
        $tasks[1]['id'] = 2;

        $response = $this->get('/api/tasks');
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll(['data', 'links', 'meta'])->has('data', 2));
        $this->assertEquals($response->json('data'), $tasks);

        $response = $this->get('/api/tasks?page=2&per_page=1&search=Task 1&sort=create_date');
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll(['data', 'links', 'meta'])->has('data', 1));
        $this->assertEquals($response->json('data')[0], $tasks[1]);
    }

    public function test_store_positive_test(): void
    {
        $this->withoutExceptionHandling();

        $task = $this->example_task;
        $response = $this->postJson('/api/tasks', $task);

        $response->assertStatus(200);
        $this->assertEquals($response->json()['message'], 'Task created successfully');
        $task['id'] = $response->json()['id'];
        $this->assertDatabaseCount('tasks', 1);
        $this->assertDatabaseHas('tasks', $task);
    }

    public function test_store_negative_test_invalid_parameters(): void
    {
        $response = $this->postJson('/api/tasks', []);
        $response->assertStatus(422);
        $response->assertJson(
            fn(AssertableJson $json) => $json->hasAll(
                'message',
                'errors',
                'errors.title',
                'errors.due_date',
                'errors.create_date',
                'errors.status',
                'errors.priority',
                'errors.category'
            )
        );

        $task = $this->example_task;
        $task['status'] = 'In progress';
        $task['priority'] = 'important';
        $response = $this->postJson('/api/tasks', $task);
        $response->assertStatus(422);
        $response->assertJson(
            fn(AssertableJson $json) => $json->hasAll(
                'message',
                'errors',
                'errors.status',
                'errors.priority',
            )
        );
    }

    public function test_show_positive_test(): void
    {
        $this->withoutExceptionHandling();

        $task = $this->example_task;
        Task::create($task);

        $response = $this->get('/api/tasks/1');
        $task['id'] = 1;

        $response->assertStatus(200);
        $this->assertEquals($response->json()['data'], $task);
    }

    public function test_show_negative_test(): void
    {
        $response = $this->get('/api/tasks/1');
        $response->assertStatus(404);
    }

    public function test_update_positive_test(): void
    {
        $this->withoutExceptionHandling();

        $task = $this->example_task;
        Task::create($task);

        $response = $this->patchJson('/api/tasks/1', []);
        $response->assertStatus(200);
        $this->assertEquals($response->json()['message'], 'Task updated successfully');
        $task['id'] = 1;
        $this->assertDatabaseCount('tasks', 1);
        $this->assertDatabaseHas('tasks', $task);

        $new_task = [
            'title' => 'New Task 1 title',
            'description' => 'New Task 1 description',
            'due_date' => now()->format('Y-m-d H:i:s'),
            'create_date' => now()->format('Y-m-d H:i:s'),
            'priority' => 'низкий',
            'category' => 'rest',
            'status' => 'выполнена',
        ];

        foreach ($new_task as $key => $value) {
            $response = $this->patchJson('/api/tasks/1', [$key => $value]);
            $task[$key] = $value;
            $response->assertStatus(200);
            $this->assertEquals($response->json()['message'], 'Task updated successfully');
            $this->assertDatabaseHas('tasks', $task);
        }

        $response = $this->patchJson('/api/tasks/1', $this->example_task);
        $task = $this->example_task;
        $task['id'] = 1;
        $response->assertStatus(200);
        $this->assertEquals($response->json()['message'], 'Task updated successfully');
        $this->assertDatabaseHas('tasks', $task);
    }

    public function test_update_negative_test(): void
    {
        $response = $this->patchJson('/api/tasks', $this->example_task);
        $response->assertStatus(405);

        $response = $this->patchJson('/api/tasks/1', $this->example_task);
        $response->assertStatus(404);
    }

    public function test_update_negative_test_invalid_parameters(): void
    {
        $task = $this->example_task;
        Task::create($task);

        $response = $this->patchJson('/api/tasks/1', ['status' => 'In progress']);
        $response->assertStatus(422);

        $response = $this->patchJson('/api/tasks/1', ['priority' => 'important']);
        $response->assertStatus(422);
    }

    public function test_destroy_positive_test(): void
    {
        $this->withoutExceptionHandling();

        $task = $this->example_task;
        Task::create($task);

        $response = $this->delete('/api/tasks/1');

        $response->assertStatus(200);
        $this->assertDatabaseCount('tasks', 0);
        $this->assertEquals($response->json()['message'], 'Task deleted successfully');
    }

    public function test_destroy_negative_test(): void
    {
        $response = $this->delete('/api/tasks');
        $response->assertStatus(405);

        $response = $this->delete('/api/tasks/1');
        $response->assertStatus(404);
    }
}

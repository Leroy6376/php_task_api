<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use app\Http\Requests\Api\StoreTaskRequest;
use app\Http\Requests\Api\TaskRequest;
use app\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\Api\TaskResource;
use App\Models\Api\Task;
use OpenApi\Attributes as OA;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[
        OA\Get(
            path: '/tasks',
            description: '',
            summary: 'Получение списка задач',
            tags: ['Task'],
            parameters: [
                new OA\Parameter(
                    parameter: 'page',
                    name: 'page',
                    description: 'Номер страницы списка',
                    in: 'query',
                    required: false,
                    schema: new OA\Schema(type: 'integer', example: 1)
                ),
                new OA\Parameter(
                    parameter: 'per_page',
                    name: 'per_page',
                    description: 'Количество задач на странице списка',
                    in: 'query',
                    required: false,
                    schema: new OA\Schema(type: 'integer', example: 10)
                ),
                new OA\Parameter(
                    parameter: 'search',
                    name: 'search',
                    description: 'Поиск по названию задачи',
                    in: 'query',
                    required: false,
                    schema: new OA\Schema(type: 'string', example: 'Задача1')
                ),
                new OA\Parameter(
                    parameter: 'sort',
                    name: 'sort',
                    description: 'Сортировка списка по полям due_date/create_date',
                    in: 'query',
                    required: false,
                    schema: new OA\Schema(type: 'string', enum: ['due_date', 'create_date'])
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'OK',
                    content: new OA\JsonContent(
                        type: 'array',
                        items: new OA\Items(ref: '#components/schemas/TaskResource'),
                    )
                )
            ]
        )
    ]
    public function index(TaskRequest $request)
    {
        $data = $request->validated();
        $search = $data['search'] ?? '';
        $per_page = $data['per_page'] ?? null;

        if (isset($data['sort'])) {
            $tasks = TaskResource::collection(
                Task::query()->whereLike('title', "%$search%")->
                orderBy($data['sort'])->paginate($per_page)->withQueryString()
            );
        } else {
            $tasks = TaskResource::collection(
                Task::query()->whereLike('title', "%$search%")->
                paginate($per_page)->withQueryString()
            );
        }
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    #[
        OA\Post(
            path: '/tasks',
            description: '',
            summary: 'Создание задачи',
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\JsonContent(
                    ref: '#components/schemas/StoreTaskRequest'
                )
            ),
            tags: ['Task'],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'OK',
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(
                                property: 'id',
                                description: 'Уникальный идентификатор задачи',
                                type: 'integer',
                                example: 1
                            ),
                            new OA\Property(
                                property: 'message',
                                description: 'Сообщение ответа',
                                type: 'string',
                                example: 'Task created successfully'
                            ),
                        ]
                    )
                ),
                new OA\Response(
                    response: 422,
                    description: 'Validation exception'
                )
            ]
        )

    ]
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $task = Task::firstOrCreate($data);

        return response()->json([
            'id' => $task->id,
            'message' => 'Task created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    #[
        OA\Get(
            path: '/tasks/{task_id}',
            description: '',
            summary: 'Получение конкретной задачи',
            tags: ['Task'],
            parameters: [
                new OA\Parameter(
                    parameter: 'task_id',
                    name: 'task_id',
                    description: 'Уникальный идентификатор задачи',
                    in: 'path',
                    required: true,
                    schema: new OA\Schema(type: 'integer', example: 1)
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'OK',
                    content: new OA\JsonContent(
                        ref: '#components/schemas/TaskResource',
                    )
                ),
                new OA\Response(
                    response: 404,
                    description: 'Task not found',
                )
            ]
        )
    ]
    public function show(Task $task)
    {
        return response()->json(new TaskResource($task));
    }

    /**
     * Update the specified resource in storage.
     */
    #[
        OA\Patch(
            path: '/tasks/{task_id}',
            description: '',
            summary: 'Обновление задачи',
            requestBody: new OA\RequestBody(
                required: false,
                content: new OA\JsonContent(
                    ref: '#components/schemas/StoreTaskRequest'
                )
            ),
            tags: ['Task'],
            parameters: [
                new OA\Parameter(
                    parameter: 'task_id',
                    name: 'task_id',
                    description: 'Уникальный идентификатор задачи',
                    in: 'path',
                    required: true,
                    schema: new OA\Schema(type: 'integer', example: 1)
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'OK',
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(
                                property: 'message',
                                description: 'Сообщение ответа',
                                type: 'string',
                                example: 'Task Task updated successfully'
                            ),
                        ]
                    )
                ),
                new OA\Response(
                    response: 404,
                    description: 'Task not found',
                ),
                new OA\Response(
                    response: 422,
                    description: 'Validation exception',
                )
            ]
        )
    ]
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        return response()->json([
            'message' => 'Task updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[
        OA\Delete(
            path: '/tasks/{task_id}',
            description: '',
            summary: 'Удаление задачи',
            tags: ['Task'],
            parameters: [
                new OA\Parameter(
                    parameter: 'task_id',
                    name: 'task_id',
                    description: 'Уникальный идентификатор задачи',
                    in: 'path',
                    required: true,
                    schema: new OA\Schema(type: 'integer', example: 1)
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'OK',
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(
                                property: 'message',
                                description: 'Сообщение ответа',
                                type: 'string',
                                example: 'Task deleted successfully'
                            ),
                        ]
                    )
                ),
                new OA\Response(
                    response: 404,
                    description: 'Task not found',
                )
            ]
        )
    ]
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(["message" => "Task deleted successfully"]);
    }
}

<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use app\Http\Requests\Api\StoreTaskRequest;
use app\Http\Requests\Api\TaskRequest;
use app\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\Api\TaskResource;
use App\Models\Api\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TaskRequest $request)
    {
        $data = $request->validated();
        $search = $data['search'] ?? '';
        $per_page = $data['per_page'] ?? null;

        if (isset($data['sort'])) {
            $tasks = TaskResource::collection(Task::query()->whereLike('title', "%$search%")->
            orderBy($data['sort'])->paginate($per_page)->withQueryString());
        } else {
            $tasks = TaskResource::collection(Task::query()->whereLike('title', "%$search%")->
            paginate($per_page)->withQueryString());
        }
        return $tasks;
    }

    /**
     * Store a newly created resource in storage.
     */
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
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
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
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(["message" => "Task deleted successfully"]);
    }
}

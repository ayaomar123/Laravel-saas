<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::latest()
            ->paginate(15);

        return response()->json($tasks);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'tenant_id' => app('tenant')->id,
            'title' => $request->title,
            'completed' => false,
        ]);

        return response()->json($task, 201);
    }

    public function show(Task $task): JsonResponse
    {
        $this->authorizeTenantAccess($task);
        
        return response()->json($task);
    }

    public function update(TaskRequest $request, Task $task): JsonResponse
    {
        $this->authorizeTenantAccess($task);
        
        $task->update($request->validated());

        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorizeTenantAccess($task);
        
        $task->delete();

        return response()->json(null, 204);
    }

    protected function authorizeTenantAccess(Task $task): void
    {
        if ($task->tenant_id !== app('tenant')->id) {
            abort(404);
        }
    }
}
<?php

namespace App\Http\Controllers\Api\Projects;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request, Project $project): JsonResponse
    {
        $query = $project->tasks()->with('employee');

        if ($request->filled('milestone_id')) {
            $query->where('milestone_id', $request->integer('milestone_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }
        $this->applyTextSearch($query, $request, ['title', 'description']);
        $this->applySort($query, $request, ['id', 'title', 'status', 'priority', 'due_date', 'milestone_id', 'employee_id', 'created_at'], 'id', 'asc');

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $data = $request->validate([
            'milestone_id' => ['nullable', 'exists:milestones,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:32'],
            'priority' => ['nullable', 'string', 'max:16'],
            'due_date' => ['nullable', 'date'],
        ]);

        if (! empty($data['milestone_id'])) {
            $belongs = $project->milestones()->whereKey($data['milestone_id'])->exists();
            if (! $belongs) {
                abort(422, 'Milestone does not belong to this project.');
            }
        }

        $task = $project->tasks()->create($data);

        return response()->json($task->load('employee'), 201);
    }

    public function show(Project $project, Task $task): JsonResponse
    {
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        return response()->json($task->load(['employee', 'milestone']));
    }

    public function update(Request $request, Project $project, Task $task): JsonResponse
    {
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $data = $request->validate([
            'milestone_id' => ['nullable', 'exists:milestones,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:32'],
            'priority' => ['nullable', 'string', 'max:16'],
            'due_date' => ['nullable', 'date'],
        ]);

        if (array_key_exists('milestone_id', $data) && $data['milestone_id']) {
            $belongs = $project->milestones()->whereKey($data['milestone_id'])->exists();
            if (! $belongs) {
                abort(422, 'Milestone does not belong to this project.');
            }
        }

        $task->update($data);

        return response()->json($task->fresh()->load(['employee', 'milestone']));
    }

    public function destroy(Project $project, Task $task): JsonResponse
    {
        if ($task->project_id !== $project->id) {
            abort(404);
        }
        $task->delete();

        return response()->json(null, 204);
    }
}

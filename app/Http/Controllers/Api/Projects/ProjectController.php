<?php

namespace App\Http\Controllers\Api\Projects;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = Project::query();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        $this->applyTextSearch($query, $request, ['name', 'code', 'description']);
        $this->applySort($query, $request, ['id', 'name', 'code', 'status', 'started_at', 'ended_at', 'created_at'], 'name', 'asc');

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:32', 'unique:projects,code'],
            'status' => ['nullable', 'string', 'max:32'],
            'description' => ['nullable', 'string'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ]);

        $project = Project::create($data);

        return response()->json($project, 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load(['milestones', 'tasks.employee']);

        return response()->json($project);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:32', 'unique:projects,code,'.$project->id],
            'status' => ['nullable', 'string', 'max:32'],
            'description' => ['nullable', 'string'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ]);

        $project->update($data);

        return response()->json($project->fresh());
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers\Api\Projects;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request, Project $project): JsonResponse
    {
        $query = $project->milestones();
        $this->applyTextSearch($query, $request, ['name']);
        $this->applySort($query, $request, ['id', 'name', 'due_date', 'sort_order', 'created_at'], 'sort_order', 'asc');

        return response()->json(
            $query->paginate($request->integer('per_page', 50))
        );
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $milestone = $project->milestones()->create($data);

        return response()->json($milestone, 201);
    }

    public function show(Project $project, Milestone $milestone): JsonResponse
    {
        if ($milestone->project_id !== $project->id) {
            abort(404);
        }
        $milestone->load('tasks');

        return response()->json($milestone);
    }

    public function update(Request $request, Project $project, Milestone $milestone): JsonResponse
    {
        if ($milestone->project_id !== $project->id) {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $milestone->update($data);

        return response()->json($milestone->fresh());
    }

    public function destroy(Project $project, Milestone $milestone): JsonResponse
    {
        if ($milestone->project_id !== $project->id) {
            abort(404);
        }
        $milestone->delete();

        return response()->json(null, 204);
    }
}

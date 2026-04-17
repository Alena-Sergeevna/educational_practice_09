<?php

namespace App\Http\Controllers\Api\Org;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = Team::withCount('employees');
        $this->applyTextSearch($query, $request, ['name', 'description']);
        $this->applySort($query, $request, ['id', 'name', 'employees_count', 'created_at'], 'name', 'asc');

        return response()->json($query->paginate($request->integer('per_page', 25)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        return response()->json(Team::create($data), 201);
    }

    public function show(Team $team): JsonResponse
    {
        return response()->json($team->load('employees'));
    }

    public function update(Request $request, Team $team): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $team->update($data);

        return response()->json($team->fresh());
    }

    public function destroy(Team $team): JsonResponse
    {
        $team->delete();

        return response()->json(null, 204);
    }

    public function attachEmployee(Request $request, Team $team): JsonResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
        ]);

        $team->employees()->syncWithoutDetaching([$data['employee_id']]);

        return response()->json($team->load('employees'));
    }

    public function detachEmployee(Team $team, Employee $employee): JsonResponse
    {
        $team->employees()->detach($employee->id);

        return response()->json($team->load('employees'));
    }
}

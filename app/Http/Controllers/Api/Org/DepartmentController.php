<?php

namespace App\Http\Controllers\Api\Org;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = Department::withCount('employees');
        $this->applyTextSearch($query, $request, ['name', 'code']);
        $this->applySort($query, $request, ['id', 'name', 'code', 'employees_count', 'created_at'], 'name', 'asc');

        return response()->json($query->paginate($request->integer('per_page', 25)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:32', 'unique:departments,code'],
        ]);

        return response()->json(Department::create($data), 201);
    }

    public function show(Department $department): JsonResponse
    {
        return response()->json($department->load('employees'));
    }

    public function update(Request $request, Department $department): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:32', 'unique:departments,code,'.$department->id],
        ]);

        $department->update($data);

        return response()->json($department->fresh());
    }

    public function destroy(Department $department): JsonResponse
    {
        $department->delete();

        return response()->json(null, 204);
    }
}

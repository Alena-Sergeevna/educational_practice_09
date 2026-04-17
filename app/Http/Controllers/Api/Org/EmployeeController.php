<?php

namespace App\Http\Controllers\Api\Org;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = Employee::with('department');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->integer('department_id'));
        }
        if ($request->filled('job_title')) {
            $query->where('job_title', 'like', '%'.$request->string('job_title').'%');
        }
        $this->applyTextSearch($query, $request, ['first_name', 'last_name', 'email', 'job_title']);
        $this->applySort($query, $request, ['id', 'first_name', 'last_name', 'email', 'job_title', 'hired_at', 'department_id', 'created_at'], 'last_name', 'asc');

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'department_id' => ['nullable', 'exists:departments,id'],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'hired_at' => ['nullable', 'date'],
        ]);

        $employee = Employee::create($data);

        return response()->json($employee->load('department'), 201);
    }

    public function show(Employee $employee): JsonResponse
    {
        return response()->json($employee->load(['department', 'teams']));
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $data = $request->validate([
            'department_id' => ['nullable', 'exists:departments,id'],
            'first_name' => ['sometimes', 'string', 'max:120'],
            'last_name' => ['sometimes', 'string', 'max:120'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:employees,email,'.$employee->id],
            'job_title' => ['nullable', 'string', 'max:255'],
            'hired_at' => ['nullable', 'date'],
        ]);

        $employee->update($data);

        return response()->json($employee->fresh()->load('department'));
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $employee->delete();

        return response()->json(null, 204);
    }
}

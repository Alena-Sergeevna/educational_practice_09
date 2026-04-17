<?php

namespace App\Http\Controllers\Api\Hiring;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = Vacancy::with('department');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->integer('department_id'));
        }
        $this->applyTextSearch($query, $request, ['title', 'description']);
        $this->applySort($query, $request, ['id', 'title', 'status', 'opened_at', 'created_at'], 'id', 'desc');

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:32'],
            'opened_at' => ['nullable', 'date'],
        ]);

        $vacancy = Vacancy::create($data);

        return response()->json($vacancy->load('department'), 201);
    }

    public function show(Vacancy $vacancy): JsonResponse
    {
        return response()->json($vacancy->load(['department', 'jobApplications.candidate']));
    }

    public function update(Request $request, Vacancy $vacancy): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:32'],
            'opened_at' => ['nullable', 'date'],
        ]);

        $vacancy->update($data);

        return response()->json($vacancy->fresh()->load('department'));
    }

    public function destroy(Vacancy $vacancy): JsonResponse
    {
        $vacancy->delete();

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers\Api\Hiring;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\JobApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request, JobApplication $job_application): JsonResponse
    {
        $query = $job_application->interviews()->with('interviewer');
        $this->applyTextSearch($query, $request, ['notes']);
        $this->applySort($query, $request, ['id', 'scheduled_at', 'interviewer_id', 'created_at'], 'scheduled_at', 'asc');

        return response()->json(
            $query->paginate($request->integer('per_page', 20))
        );
    }

    public function store(Request $request, JobApplication $job_application): JsonResponse
    {
        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'interviewer_id' => ['nullable', 'exists:employees,id'],
        ]);

        $interview = $job_application->interviews()->create($data);

        return response()->json($interview->load('interviewer'), 201);
    }

    public function show(JobApplication $job_application, Interview $interview): JsonResponse
    {
        if ($interview->job_application_id !== $job_application->id) {
            abort(404);
        }

        return response()->json($interview->load('interviewer'));
    }

    public function update(Request $request, JobApplication $job_application, Interview $interview): JsonResponse
    {
        if ($interview->job_application_id !== $job_application->id) {
            abort(404);
        }

        $data = $request->validate([
            'scheduled_at' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
            'interviewer_id' => ['nullable', 'exists:employees,id'],
        ]);

        $interview->update($data);

        return response()->json($interview->fresh()->load('interviewer'));
    }

    public function destroy(JobApplication $job_application, Interview $interview): JsonResponse
    {
        if ($interview->job_application_id !== $job_application->id) {
            abort(404);
        }
        $interview->delete();

        return response()->json(null, 204);
    }
}

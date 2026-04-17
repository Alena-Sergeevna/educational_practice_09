<?php

namespace App\Http\Controllers\Api\Hiring;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Vacancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = JobApplication::with(['vacancy', 'candidate']);

        if ($request->filled('vacancy_id')) {
            $query->where('vacancy_id', $request->integer('vacancy_id'));
        }
        if ($request->filled('stage')) {
            $query->where('stage', $request->string('stage'));
        }
        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->integer('candidate_id'));
        }
        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where(function ($sub) use ($term) {
                $sub->whereHas('candidate', function ($c) use ($term) {
                    $c->where('full_name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                })->orWhereHas('vacancy', function ($v) use ($term) {
                    $v->where('title', 'like', $term);
                });
            });
        }
        $this->applySort($query, $request, ['id', 'stage', 'applied_at', 'created_at'], 'id', 'desc');

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function indexByVacancy(Request $request, Vacancy $vacancy): JsonResponse
    {
        $query = $vacancy->jobApplications()->with('candidate');
        if ($request->filled('stage')) {
            $query->where('stage', $request->string('stage'));
        }
        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->whereHas('candidate', function ($c) use ($term) {
                $c->where('full_name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }
        $this->applySort($query, $request, ['id', 'stage', 'applied_at', 'created_at'], 'id', 'desc');

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'vacancy_id' => ['required', 'exists:vacancies,id'],
            'candidate_id' => ['required', 'exists:candidates,id'],
            'stage' => ['nullable', 'string', 'max:64'],
            'applied_at' => ['nullable', 'date'],
        ]);

        $exists = JobApplication::where('vacancy_id', $data['vacancy_id'])
            ->where('candidate_id', $data['candidate_id'])
            ->exists();

        if ($exists) {
            abort(422, 'This candidate has already applied to this vacancy.');
        }

        $application = JobApplication::create($data);

        return response()->json($application->load(['vacancy', 'candidate']), 201);
    }

    public function show(JobApplication $job_application): JsonResponse
    {
        return response()->json($job_application->load(['vacancy', 'candidate', 'interviews.interviewer']));
    }

    public function update(Request $request, JobApplication $job_application): JsonResponse
    {
        $data = $request->validate([
            'stage' => ['sometimes', 'string', 'max:64'],
            'applied_at' => ['nullable', 'date'],
        ]);

        $job_application->update($data);

        return response()->json($job_application->fresh()->load(['vacancy', 'candidate']));
    }

    public function destroy(JobApplication $job_application): JsonResponse
    {
        $job_application->delete();

        return response()->json(null, 204);
    }
}

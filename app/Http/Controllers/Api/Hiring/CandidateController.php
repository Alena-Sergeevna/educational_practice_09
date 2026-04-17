<?php

namespace App\Http\Controllers\Api\Hiring;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = Candidate::query();
        $this->applyTextSearch($query, $request, ['full_name', 'email', 'phone']);
        $this->applySort($query, $request, ['id', 'full_name', 'email', 'phone', 'created_at'], 'full_name', 'asc');

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:candidates,email'],
            'phone' => ['nullable', 'string', 'max:64'],
        ]);

        return response()->json(Candidate::create($data), 201);
    }

    public function show(Candidate $candidate): JsonResponse
    {
        return response()->json($candidate->load('jobApplications.vacancy'));
    }

    public function update(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:candidates,email,'.$candidate->id],
            'phone' => ['nullable', 'string', 'max:64'],
        ]);

        $candidate->update($data);

        return response()->json($candidate->fresh());
    }

    public function destroy(Candidate $candidate): JsonResponse
    {
        $candidate->delete();

        return response()->json(null, 204);
    }
}

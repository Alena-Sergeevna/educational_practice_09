<?php

namespace App\Http\Controllers\Api\Assets;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\SoftwareLicense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SoftwareLicenseController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = SoftwareLicense::query()->withCount(['employees as used_seats']);
        $this->applyTextSearch($query, $request, ['name']);
        $this->applySort($query, $request, ['id', 'name', 'total_seats', 'expires_at', 'created_at'], 'name', 'asc');

        return response()->json(
            $query->paginate($request->integer('per_page', 15))
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'total_seats' => ['required', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $license = SoftwareLicense::create($data);
        $license->setAttribute('used_seats', 0);

        return response()->json($license, 201);
    }

    public function show(SoftwareLicense $software_license): JsonResponse
    {
        $software_license->load('employees');
        $software_license->setAttribute('used_seats', $software_license->usedSeatsCount());

        return response()->json($software_license);
    }

    public function update(Request $request, SoftwareLicense $software_license): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'total_seats' => ['sometimes', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ]);

        if (isset($data['total_seats']) && $data['total_seats'] < $software_license->usedSeatsCount()) {
            abort(422, 'total_seats cannot be less than assigned seats.');
        }

        $software_license->update($data);
        $software_license->setAttribute('used_seats', $software_license->usedSeatsCount());

        return response()->json($software_license->fresh(['employees']));
    }

    public function destroy(SoftwareLicense $software_license): JsonResponse
    {
        $software_license->delete();

        return response()->json(null, 204);
    }

    public function attachEmployee(Request $request, SoftwareLicense $software_license): JsonResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
        ]);

        if ($software_license->usedSeatsCount() >= $software_license->total_seats) {
            abort(422, 'No free seats on this license.');
        }

        $software_license->employees()->syncWithoutDetaching([$data['employee_id']]);

        return response()->json($this->licenseWithUsage($software_license->fresh()->load('employees')));
    }

    public function detachEmployee(SoftwareLicense $software_license, Employee $employee): JsonResponse
    {
        $software_license->employees()->detach($employee->id);

        return response()->json($this->licenseWithUsage($software_license->fresh()->load('employees')));
    }

    private function licenseWithUsage(SoftwareLicense $license): SoftwareLicense
    {
        $license->setAttribute('used_seats', $license->usedSeatsCount());

        return $license;
    }
}

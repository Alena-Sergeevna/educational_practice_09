<?php

namespace App\Http\Controllers\Api\Assets;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\HardwareAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HardwareAssetController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = HardwareAsset::with('employee');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }
        $this->applyTextSearch($query, $request, ['name', 'inventory_number', 'type', 'notes']);
        $this->applySort($query, $request, ['id', 'name', 'inventory_number', 'status', 'type', 'employee_id', 'created_at'], 'inventory_number', 'asc');

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:64'],
            'inventory_number' => ['required', 'string', 'max:64', 'unique:hardware_assets,inventory_number'],
            'status' => ['nullable', 'string', 'max:32'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'notes' => ['nullable', 'string'],
        ]);

        if (($data['status'] ?? 'in_stock') === 'assigned' && empty($data['employee_id'])) {
            abort(422, 'employee_id is required when status is assigned.');
        }

        $asset = HardwareAsset::create($data);

        return response()->json($asset->load('employee'), 201);
    }

    public function show(HardwareAsset $hardwareAsset): JsonResponse
    {
        return response()->json($hardwareAsset->load('employee'));
    }

    public function update(Request $request, HardwareAsset $hardwareAsset): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:64'],
            'inventory_number' => ['sometimes', 'string', 'max:64', 'unique:hardware_assets,inventory_number,'.$hardwareAsset->id],
            'status' => ['nullable', 'string', 'max:32'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $hardwareAsset->update($data);

        $fresh = $hardwareAsset->fresh();
        if ($fresh->status === 'in_stock' || $fresh->status === 'retired') {
            $fresh->employee_id = null;
            $fresh->save();
        }
        if ($fresh->status === 'assigned' && ! $fresh->employee_id) {
            abort(422, 'employee_id is required when status is assigned.');
        }

        return response()->json($fresh->load('employee'));
    }

    public function destroy(HardwareAsset $hardwareAsset): JsonResponse
    {
        $hardwareAsset->delete();

        return response()->json(null, 204);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Assets;

use App\Http\Controllers\Controller;
use App\Models\HardwareAsset;
use App\Models\SoftwareLicense;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AssetsAnalyticsController extends Controller
{
    /** Оборудование по статусам */
    public function hardwareByStatus(): JsonResponse
    {
        $rows = HardwareAsset::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json(['hardware_by_status' => $rows]);
    }

    /** Лицензии: занято мест и процент утилизации */
    public function licenseUtilization(): JsonResponse
    {
        $licenses = SoftwareLicense::query()->orderBy('name')->get();
        $rows = $licenses->map(function (SoftwareLicense $license) {
            $used = $license->usedSeatsCount();
            $total = max(1, (int) $license->total_seats);

            return [
                'id' => $license->id,
                'name' => $license->name,
                'total_seats' => $license->total_seats,
                'used_seats' => $used,
                'free_seats' => max(0, $license->total_seats - $used),
                'utilization_percent' => round(100 * $used / $total, 1),
                'expires_at' => $license->expires_at?->toDateString(),
            ];
        });

        return response()->json(['licenses' => $rows]);
    }

    /** Оборудование по типам (поле type) */
    public function hardwareByType(): JsonResponse
    {
        $rows = HardwareAsset::query()
            ->select('type', DB::raw('count(*) as count'))
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->groupBy('type')
            ->orderByDesc('count')
            ->get();

        return response()->json(['hardware_by_type' => $rows]);
    }
}

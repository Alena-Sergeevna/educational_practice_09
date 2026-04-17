<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Org;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrgAnalyticsController extends Controller
{
    /** Сотрудники по отделам */
    public function employeesByDepartment(): JsonResponse
    {
        $rows = Department::query()
            ->withCount('employees')
            ->orderByDesc('employees_count')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json(['departments' => $rows]);
    }

    /** Команды: размер и список id участников (только счётчики) */
    public function teamSizes(): JsonResponse
    {
        $rows = Team::query()
            ->withCount('employees')
            ->orderByDesc('employees_count')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['teams' => $rows]);
    }

    /** Должности: сколько сотрудников с каждым job_title (не null) */
    public function jobTitleDistribution(): JsonResponse
    {
        $rows = DB::table('employees')
            ->select('job_title', DB::raw('count(*) as count'))
            ->whereNotNull('job_title')
            ->where('job_title', '!=', '')
            ->groupBy('job_title')
            ->orderByDesc('count')
            ->get();

        return response()->json(['job_titles' => $rows]);
    }
}

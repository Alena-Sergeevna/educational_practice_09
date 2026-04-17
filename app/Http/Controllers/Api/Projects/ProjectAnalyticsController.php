<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProjectAnalyticsController extends Controller
{
    /** Сводка: проекты и задачи по статусам, просроченные задачи */
    public function overview(): JsonResponse
    {
        $projectsByStatus = Project::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $tasksByStatus = Task::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $overdueTasks = Task::query()
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString())
            ->whereNotIn('status', ['done', 'cancelled', 'closed'])
            ->count();

        return response()->json([
            'projects_by_status' => $projectsByStatus,
            'tasks_by_status' => $tasksByStatus,
            'overdue_tasks_open' => $overdueTasks,
            'projects_total' => Project::query()->count(),
            'tasks_total' => Task::query()->count(),
        ]);
    }

    /** Задачи по приоритетам */
    public function tasksByPriority(): JsonResponse
    {
        $rows = Task::query()
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get();

        return response()->json(['tasks_by_priority' => $rows]);
    }

    /** Нагрузка: число задач на исполнителя (топ) */
    public function taskWorkload(): JsonResponse
    {
        $rows = Task::query()
            ->select('employee_id', DB::raw('count(*) as tasks_count'))
            ->whereNotNull('employee_id')
            ->groupBy('employee_id')
            ->orderByDesc('tasks_count')
            ->with('employee:id,first_name,last_name,email')
            ->limit(50)
            ->get();

        return response()->json(['workload' => $rows]);
    }
}

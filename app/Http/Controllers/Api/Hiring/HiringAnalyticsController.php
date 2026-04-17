<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Hiring;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\Vacancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HiringAnalyticsController extends Controller
{
    /** Воронка: отклики по этапам */
    public function applicationsByStage(): JsonResponse
    {
        $rows = JobApplication::query()
            ->select('stage', DB::raw('count(*) as count'))
            ->groupBy('stage')
            ->orderByDesc('count')
            ->get();

        return response()->json(['applications_by_stage' => $rows]);
    }

    /** Вакансии: сколько откликов на каждую */
    public function vacancyApplications(): JsonResponse
    {
        $rows = Vacancy::query()
            ->withCount('jobApplications')
            ->orderByDesc('job_applications_count')
            ->get(['id', 'title', 'status', 'job_applications_count']);

        return response()->json(['vacancies' => $rows]);
    }

    /** Предстоящие интервью (по умолчанию ближайшие 30 дней) */
    public function upcomingInterviews(Request $request): JsonResponse
    {
        $days = $request->integer('days', 30);
        $days = min(365, max(1, $days));

        $from = now();
        $to = now()->addDays($days);

        $rows = Interview::query()
            ->whereBetween('scheduled_at', [$from, $to])
            ->with([
                'jobApplication.vacancy:id,title',
                'jobApplication.candidate:id,full_name,email',
                'interviewer:id,first_name,last_name',
            ])
            ->orderBy('scheduled_at')
            ->limit(200)
            ->get();

        return response()->json([
            'period_days' => $days,
            'from' => $from->toIso8601String(),
            'to' => $to->toIso8601String(),
            'interviews' => $rows,
        ]);
    }
}

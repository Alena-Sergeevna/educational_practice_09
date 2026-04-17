<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TicketsAnalyticsController extends Controller
{
    /** Заявки по статусам */
    public function recordsByStatus(): JsonResponse
    {
        $rows = Ticket::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json(['records_by_status' => $rows]);
    }

    /** Заявки по категориям */
    public function recordsByCategory(): JsonResponse
    {
        $rows = TicketCategory::query()
            ->withCount('tickets')
            ->orderByDesc('tickets_count')
            ->get(['id', 'name', 'slug', 'tickets_count']);

        return response()->json(['records_by_category' => $rows]);
    }

    /** Заявки по приоритету */
    public function recordsByPriority(): JsonResponse
    {
        $rows = Ticket::query()
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get();

        return response()->json(['records_by_priority' => $rows]);
    }
}

<?php

namespace App\Http\Controllers\Api\Tickets;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = Ticket::with(['category', 'reporter', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('ticket_category_id')) {
            $query->where('ticket_category_id', $request->integer('ticket_category_id'));
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }
        if ($request->filled('assignee_id')) {
            $query->where('assignee_id', $request->integer('assignee_id'));
        }
        if ($request->filled('reporter_id')) {
            $query->where('reporter_id', $request->integer('reporter_id'));
        }
        $this->applyTextSearch($query, $request, ['subject', 'body']);
        $this->applySort($query, $request, ['id', 'subject', 'status', 'priority', 'created_at', 'updated_at'], 'id', 'desc');

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ticket_category_id' => ['required', 'exists:ticket_categories,id'],
            'reporter_id' => ['required', 'exists:employees,id'],
            'assignee_id' => ['nullable', 'exists:employees,id'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', 'max:16'],
            'status' => ['nullable', 'string', 'max:32'],
        ]);

        $ticket = Ticket::create($data);

        return response()->json($ticket->load(['category', 'reporter', 'assignee']), 201);
    }

    public function show(Ticket $record): JsonResponse
    {
        return response()->json($record->load(['category', 'reporter', 'assignee', 'comments.employee']));
    }

    public function update(Request $request, Ticket $record): JsonResponse
    {
        $data = $request->validate([
            'ticket_category_id' => ['sometimes', 'exists:ticket_categories,id'],
            'reporter_id' => ['sometimes', 'exists:employees,id'],
            'assignee_id' => ['nullable', 'exists:employees,id'],
            'subject' => ['sometimes', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', 'max:16'],
            'status' => ['nullable', 'string', 'max:32'],
        ]);

        $record->update($data);

        return response()->json($record->fresh()->load(['category', 'reporter', 'assignee']));
    }

    public function destroy(Ticket $record): JsonResponse
    {
        $record->delete();

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers\Api\Tickets;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request, Ticket $record): JsonResponse
    {
        $query = $record->comments()->with('employee');
        $this->applyTextSearch($query, $request, ['body']);
        $this->applySort($query, $request, ['id', 'created_at', 'updated_at', 'employee_id'], 'id', 'asc');

        return response()->json(
            $query->paginate($request->integer('per_page', 30))
        );
    }

    public function store(Request $request, Ticket $record): JsonResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'body' => ['required', 'string'],
        ]);

        $comment = $record->comments()->create($data);

        return response()->json($comment->load('employee'), 201);
    }

    public function show(Ticket $record, TicketComment $ticket_comment): JsonResponse
    {
        if ($ticket_comment->ticket_id !== $record->id) {
            abort(404);
        }

        return response()->json($ticket_comment->load('employee'));
    }

    public function update(Request $request, Ticket $record, TicketComment $ticket_comment): JsonResponse
    {
        if ($ticket_comment->ticket_id !== $record->id) {
            abort(404);
        }

        $data = $request->validate([
            'body' => ['sometimes', 'string'],
        ]);

        $ticket_comment->update($data);

        return response()->json($ticket_comment->fresh()->load('employee'));
    }

    public function destroy(Ticket $record, TicketComment $ticket_comment): JsonResponse
    {
        if ($ticket_comment->ticket_id !== $record->id) {
            abort(404);
        }
        $ticket_comment->delete();

        return response()->json(null, 204);
    }
}

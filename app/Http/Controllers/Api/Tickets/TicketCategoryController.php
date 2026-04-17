<?php

namespace App\Http\Controllers\Api\Tickets;

use App\Http\Controllers\Concerns\AppliesListQuery;
use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketCategoryController extends Controller
{
    use AppliesListQuery;

    public function index(Request $request): JsonResponse
    {
        $query = TicketCategory::withCount('tickets');
        $this->applyTextSearch($query, $request, ['name', 'slug']);
        $this->applySort($query, $request, ['id', 'name', 'slug', 'tickets_count', 'created_at'], 'name', 'asc');

        return response()->json($query->paginate($request->integer('per_page', 25)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:ticket_categories,slug'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);
        }

        return response()->json(TicketCategory::create($data), 201);
    }

    public function show(TicketCategory $ticket_category): JsonResponse
    {
        return response()->json($ticket_category);
    }

    public function update(Request $request, TicketCategory $ticket_category): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:ticket_categories,slug,'.$ticket_category->id],
        ]);

        $ticket_category->update($data);

        return response()->json($ticket_category->fresh());
    }

    public function destroy(TicketCategory $ticket_category): JsonResponse
    {
        $ticket_category->delete();

        return response()->json(null, 204);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait AppliesListQuery
{
    /**
     * @param  list<string>  $columns  Column names on the query model table
     */
    protected function applyTextSearch(Builder $query, Request $request, array $columns): void
    {
        if (! $request->filled('q')) {
            return;
        }

        $term = '%'.$request->string('q').'%';
        $query->where(function ($sub) use ($columns, $term) {
            foreach ($columns as $i => $col) {
                if ($i === 0) {
                    $sub->where($col, 'like', $term);
                } else {
                    $sub->orWhere($col, 'like', $term);
                }
            }
        });
    }

    /**
     * @param  list<string>  $allowedColumns
     */
    protected function applySort(
        Builder $query,
        Request $request,
        array $allowedColumns,
        string $defaultColumn = 'id',
        string $defaultDirection = 'asc'
    ): void {
        $sort = (string) $request->input('sort', $defaultColumn);
        $dir = strtolower((string) $request->input('direction', $defaultDirection)) === 'desc' ? 'desc' : 'asc';

        if (! in_array($sort, $allowedColumns, true)) {
            $sort = $defaultColumn;
        }

        $query->orderBy($sort, $dir);
    }
}

<?php

namespace App\Traits;

use App\Actions\AdvancedSearch;
use App\Enums\HeaderKey;
use App\Facades\Database;
use Illuminate\Database\Eloquent\Builder;

trait TableFilter
{

    public function scopeSearch(Builder $query, string|array|AdvancedSearch $column, bool $encrypted = false): void
    {
        $search = request()->query('search');

        if (!$search) {
            return;
        }

        if (is_array($column)) {
            foreach ($column as $key => $col) {
                if ($key == 0) {
                    $query->where($col, "LIKE", "%{$search}%");
                    continue;
                }

                $query->orWhere($col, "LIKE", "%{$search}%");
            }
            return;
        }

        if (is_string($column)) {
            $query->where($column, "LIKE", "%{$search}%");
            return;
        }

        if ($column instanceof AdvancedSearch) {
            $q = $query;
            if ($column->getParent()) {
                if (!$q->first()) {
                    return;
                }

                if (!$ids = $q->first()->pluck('id')) {
                    return;
                }

                if ($model = Database::queryBuilder($column->getParent())
                    ->whereIn($column->getForeignString(), $ids)
                    ->where('mode', request()->header(HeaderKey::MODE->value))
                    ->get())
                {

                    if ($foreignKeyValue = $column->search($model, $search)) {
                        $query->where('id', $foreignKeyValue);
                        return;
                    }

                }

                return;
            }

            if ($model = $q->with([$column->getRelationship()])
                    ->first()?->{$column->getRelationship()})
            {

                if ($foreignKeyValues = $column->search($model, $search)) {
                    $query->whereIn($column->getForeignString(), $foreignKeyValues);
                    return;
                }

                return;
            }
        }
    }

    public function scopeDate(Builder $query, string $dateColumn = 'created_at'): void
    {
        $whiteList = ['created_at', 'updated_at'];
        $dateColumn = request()->query('date', 'created_at');

        if (!in_array(strtolower($dateColumn), $whiteList))
        {
            return;
        }

        $from = request()->query('from');
        $to = request()->query('to');

        if (!($from && $to)) {
            return;
        }

        if ($from && !$to) {
            $query->where($dateColumn, '>=', $from);
        }

        if (!$from && $to) {
            $query->whereNot($dateColumn, '>=', $to);
        }

        $query->whereBetween($dateColumn, [$from, $to]);
    }

    public function scopeOrder(Builder $query, string $column = 'id'): void
    {
        $whiteList = ['asc', 'desc'];
        $order = request()->query('order', 'desc');

        if (!in_array(strtolower($order), $whiteList))
        {
            $query->orderBy($column, 'desc');
            return;
        }

        $query->orderBy($column, $order);
    }

    public function scopeFilter(Builder $query, array $whiteList): void
    {
        $filterColumn = request()->query('filterColumn');
        $filterValue = request()->query('filterValue');

        if (!$filterColumn) {
            return;
        }

        if (!$filterValue) {
            return;
        }

        $columns = explode(',', $filterColumn);
        $values = explode(',', $filterValue);

        foreach ($columns as $key => $column) {
            if (!in_array(strtolower($column), $whiteList)) {
                continue;
            }

            if (isset($columns[$key]) && isset($values[$key])) {
                if (strtolower($values[$key]) == 'null') {
                    $query->whereNull($column);
                } else {
                    $query->where($column, $values[$key]);
                }
            }
        }
    }
}

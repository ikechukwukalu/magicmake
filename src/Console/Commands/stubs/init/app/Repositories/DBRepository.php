<?php

namespace App\Repositories;

use App\Contracts\DBRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DBRepository implements DBRepositoryInterface
{

    /**
     * Fetch all \Illuminate\Support\Facades\DB records.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll(string $table): Collection
    {
        return DB::table($table)->get();
    }

    /**
     * Fetch \Illuminate\Support\Facades\DB record by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id, string $table): mixed
    {
        return DB::table($table)->where('id', $id)->first();
    }

    /**
     * Delete \Illuminate\Support\Facades\DB record by ID.
     *
     * @param int $id
     * @return int
     */
    public function delete(int $id, string $table): int
    {
        return DB::table($table)->delete($id);
    }

    /**
     * Create \Illuminate\Support\Facades\DB record.
     *
     * @param array $arrayDetails
     * @param string $table
     * @return int
     */
    public function create(array $arrayDetails, string $table): int
    {
        return DB::table($table)->insertGetId($arrayDetails);
    }

    /**
     * Update \Illuminate\Support\Facades\DB record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails, string $table): int
    {
        return DB::table($table)->where('id', $id)->update($arrayDetails);
    }

    /**
     * Update Multiple \Illuminate\Support\Facades\DB record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return int
     */
    public function updateWhereIn(array $matchDetails, array $arrayDetails, string $table): int
    {
        return DB::table($table)->whereIn('id', $matchDetails)->update($arrayDetails);
    }

    /**
     * Update \Illuminate\Support\Facades\DB record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize, string $table): LengthAwarePaginator
    {
        return DB::table($table)->paginate($pageSize);
    }

    /**
     * Query builder for validation
     *
     * @param string $table
     * @return Builder
     */
    public function queryBuilder(string $table): Builder
    {
        return DB::table($table);
    }

    /**
     * Update or create \Illuminate\Support\Facades\DB record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @param string $table
     * @return bool
     */
    public function updateOrCreate(array $matchDetails, array $arrayDetails, string $table): bool
    {
        return DB::table($table)->updateOrInsert($matchDetails, $arrayDetails);
    }

    /**
     * Create \Illuminate\Support\Facades\DB record.
     *
     * @param array $arrayDetails
     * @param string $table
     * @return bool
     */
    public function insert(array $arrayDetails, string $table): bool
    {
        return DB::table($table)->insert($arrayDetails);
    }
}

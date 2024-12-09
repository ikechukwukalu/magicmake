<?php

namespace App\Contracts;

use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DBRepositoryInterface
{

    /**
     * Fetch all \Illuminate\Support\Facades\DB records.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll(string $table): Collection;

    /**
     * Fetch \Illuminate\Support\Facades\DB record by ID.
     *
     * @param int $id
     * @param string $table
     * @return mixed
     */
    public function getById(int $id, string $table): mixed;

    /**
     * Delete \Illuminate\Support\Facades\DB record by ID.
     *
     * @param int $id
     * @param string $table
     * @return int
     */
    public function delete(int $id, string $table): int;

    /**
     * Create \Illuminate\Support\Facades\DB record.
     *
     * @param array $arrayDetails
     * @param string $table
     * @return int
     */
    public function create(array $arrayDetails, string $table): int;

    /**
     * Update \Illuminate\Support\Facades\DB record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @param string $table
     * @return int
     */
    public function update(int $id, array $arrayDetails, string $table): int;

    /**
     * Update Multiple \Illuminate\Support\Facades\DB record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return int
     */
    public function updateWhereIn(array $matchDetails, array $arrayDetails, string $table): int;

    /**
     * Update \Illuminate\Support\Facades\DB record.
     *
     * @param int $pageSize
     * @param string $table
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize, string $table): LengthAwarePaginator;

    /**
     * Query builder for validation
     *
     * @param string $table
     * @return Builder
     */
    public function queryBuilder(string $table): Builder;

    /**
     * Update or create \Illuminate\Support\Facades\DB record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @param string $table
     * @return bool
     */
    public function updateOrCreate(array $matchDetails, array $arrayDetails, string $table): bool;

    /**
     * Create \Illuminate\Support\Facades\DB record.
     *
     * @param array $arrayDetails
     * @param string $table
     * @return bool
     */
    public function insert(array $arrayDetails, string $table): bool;
}

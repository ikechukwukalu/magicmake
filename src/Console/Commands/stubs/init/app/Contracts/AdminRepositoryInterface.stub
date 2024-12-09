<?php

namespace App\Contracts;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdminRepositoryInterface
{

    /**
     * Fetch all \App\Models\Admin records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection;

    /**
     * Fetch \App\Models\Admin record by ID.
     *
     * @param int $id
     * @return \App\Models\Admin|null
     */
    public function getById(int $id): null|Admin;

    /**
     * Delete \App\Models\Admin record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Create \App\Models\Admin record.
     *
     * @param array $arrayDetails
     * @return \App\Models\Admin
     */
    public function create(array $arrayDetails): Admin;

    /**
     * Fetch or create a single \App\Models\Admin record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\Admin
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): Admin;

    /**
     * Update \App\Models\Admin record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int;

    /**
     * Update \App\Models\Admin record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator;
}

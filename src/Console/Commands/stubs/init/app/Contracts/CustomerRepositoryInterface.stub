<?php

namespace App\Contracts;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{

    /**
     * Fetch all \App\Models\Customer records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection;

    /**
     * Fetch \App\Models\Customer record by ID.
     *
     * @param int $id
     * @return \App\Models\Customer|null
     */
    public function getById(int $id): null|Customer;

    /**
     * Delete \App\Models\Customer record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Create \App\Models\Customer record.
     *
     * @param array $arrayDetails
     * @return \App\Models\Customer
     */
    public function create(array $arrayDetails): Customer;

    /**
     * Fetch or create a single \App\Models\Customer record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\Customer
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): Customer;

    /**
     * Update \App\Models\Customer record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int;

    /**
     * Update \App\Models\Customer record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator;
}

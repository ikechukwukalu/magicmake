<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{

    /**
     * Fetch all \App\Models\User records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection;

    /**
     * Fetch \App\Models\User record by ID.
     *
     * @param int $id
     * @return \App\Models\User|null
     */
    public function getById(int $id): null|User;

    /**
     * Delete \App\Models\User record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Create \App\Models\User record.
     *
     * @param array $arrayDetails
     * @return \App\Models\User
     */
    public function create(array $arrayDetails): User;

    /**
     * Fetch or create a single \App\Models\User record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\User
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): User;

    /**
     * Update \App\Models\User record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int;

    /**
     * Update \App\Models\User record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator;

    /**
     * Fetch \App\Models\User record(s) by email.
     *
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getByEmail(string $email): Builder;
}

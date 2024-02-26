<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\UserPasswordHolder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserPasswordHolderRepositoryInterface
{

    /**
     * Fetch all \App\Models\UserPasswordHolder records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection;

    /**
     * Fetch \App\Models\UserPasswordHolder record by ID.
     *
     * @param int $id
     * @return \App\Models\UserPasswordHolder|null
     */
    public function getById(int $id): null|UserPasswordHolder;

    /**
     * Delete \App\Models\UserPasswordHolder record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Create \App\Models\UserPasswordHolder record.
     *
     * @param array $arrayDetails
     * @return \App\Models\UserPasswordHolder
     */
    public function create(array $arrayDetails): UserPasswordHolder;

    /**
     * Fetch or create a single \App\Models\UserPasswordHolder record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\UserPasswordHolder
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): UserPasswordHolder;

    /**
     * Update \App\Models\UserPasswordHolder record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int;

    /**
     * Update \App\Models\UserPasswordHolder record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator;

    /**
     * Fetch \App\Models\UserPasswordHolder record by ID.
     *
     * @param \App\Models\User $user
     * @return \App\Models\UserPasswordHolder|null
     */
    public function belongsToUser(User $user): null|UserPasswordHolder;
}

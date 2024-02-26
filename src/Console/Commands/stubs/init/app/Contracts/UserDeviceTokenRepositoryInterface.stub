<?php

namespace App\Contracts;

use App\Models\UserDeviceToken;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserDeviceTokenRepositoryInterface
{

    /**
     * Fetch all \App\Models\UserDeviceToken records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection;

    /**
     * Fetch \App\Models\UserDeviceToken record by ID.
     *
     * @param int $id
     * @return \App\Models\UserDeviceToken|null
     */
    public function getById(int $id): null|UserDeviceToken;

    /**
     * Delete \App\Models\UserDeviceToken record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Create \App\Models\UserDeviceToken record.
     *
     * @param array $arrayDetails
     * @return \App\Models\UserDeviceToken
     */
    public function create(array $arrayDetails): UserDeviceToken;

    /**
     * Fetch or create a single \App\Models\UserDeviceToken record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\UserDeviceToken
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): UserDeviceToken;

    /**
     * Update \App\Models\UserDeviceToken record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int;

    /**
     * Update \App\Models\UserDeviceToken record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator;
}

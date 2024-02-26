<?php

namespace App\Contracts;

use App\Models\TwoFactorLogin;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TwoFactorLoginRepositoryInterface
{

    /**
     * Fetch all \App\Models\TwoFactorLogin records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection;

    /**
     * Fetch \App\Models\TwoFactorLogin record by ID.
     *
     * @param int $id
     * @return \App\Models\TwoFactorLogin|null
     */
    public function getById(int $id): null|TwoFactorLogin;

    /**
     * Delete \App\Models\TwoFactorLogin record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Create \App\Models\TwoFactorLogin record.
     *
     * @param array $arrayDetails
     * @return \App\Models\TwoFactorLogin
     */
    public function create(array $arrayDetails): TwoFactorLogin;

    /**
     * Fetch or create a single \App\Models\TwoFactorLogin record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\TwoFactorLogin
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): TwoFactorLogin;

    /**
     * Update \App\Models\TwoFactorLogin record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int;

    /**
     * Update \App\Models\TwoFactorLogin record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator;
}

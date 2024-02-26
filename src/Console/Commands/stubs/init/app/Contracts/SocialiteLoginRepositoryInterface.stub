<?php

namespace App\Contracts;

use App\Models\SocialiteLogin;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SocialiteLoginRepositoryInterface
{

    /**
     * Fetch all \App\Models\SocialiteLogin records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection;

    /**
     * Fetch \App\Models\SocialiteLogin record by ID.
     *
     * @param int $id
     * @return \App\Models\SocialiteLogin|null
     */
    public function getById(int $id): null|SocialiteLogin;

    /**
     * Delete \App\Models\SocialiteLogin record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Create \App\Models\SocialiteLogin record.
     *
     * @param array $arrayDetails
     * @return \App\Models\SocialiteLogin
     */
    public function create(array $arrayDetails): SocialiteLogin;

    /**
     * Fetch or create a single \App\Models\SocialiteLogin record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\SocialiteLogin
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): SocialiteLogin;

    /**
     * Update \App\Models\SocialiteLogin record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int;

    /**
     * Update \App\Models\SocialiteLogin record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator;

    /**
     * Fetch \App\Models\SocialiteLogin record by User Uuid.
     *
     * @param string $userUUID
     * @return \App\Models\SocialiteLogin|null
     */
    public function getByUserUuid(string $userUUID): null|SocialiteLogin;

}

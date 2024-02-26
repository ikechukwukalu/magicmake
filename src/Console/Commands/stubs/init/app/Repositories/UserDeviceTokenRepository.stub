<?php

namespace App\Repositories;

use App\Contracts\UserDeviceTokenRepositoryInterface;
use App\Models\UserDeviceToken;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserDeviceTokenRepository implements UserDeviceTokenRepositoryInterface
{

    /**
     * Fetch all \App\Models\UserDeviceToken records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection
    {
        return UserDeviceToken::all();
    }

    /**
     * Fetch \App\Models\UserDeviceToken record by ID.
     *
     * @param int $id
     * @return \App\Models\UserDeviceToken|null
     */
    public function getById(int $id): null|UserDeviceToken
    {
        return UserDeviceToken::find($id);
    }

    /**
     * Delete \App\Models\UserDeviceToken record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        UserDeviceToken::destroy($id);
    }

    /**
     * Create \App\Models\UserDeviceToken record.
     *
     * @param array $arrayDetails
     * @return \App\Models\UserDeviceToken
     */
    public function create(array $arrayDetails): UserDeviceToken
    {
        return UserDeviceToken::create($arrayDetails);
    }

    /**
     * Fetch or create a single \App\Models\UserDeviceToken record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\UserDeviceToken
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): UserDeviceToken
    {
        return UserDeviceToken::firstOrCreate($matchDetails, $arrayDetails);
    }

    /**
     * Update \App\Models\UserDeviceToken record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int
    {
        return UserDeviceToken::where('id', $id)->update($arrayDetails);
    }

    /**
     * Update \App\Models\UserDeviceToken record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator
    {
        return UserDeviceToken::paginate($pageSize);
    }
}

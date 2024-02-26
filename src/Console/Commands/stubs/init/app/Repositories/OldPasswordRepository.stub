<?php

namespace App\Repositories;

use App\Contracts\OldPasswordRepositoryInterface;
use App\Models\OldPassword;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class OldPasswordRepository implements OldPasswordRepositoryInterface
{

    /**
     * Fetch all \App\Models\OldPassword records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection
    {
        return OldPassword::all();
    }

    /**
     * Fetch \App\Models\OldPassword record by ID.
     *
     * @param int $id
     * @return \App\Models\OldPassword|null
     */
    public function getById(int $id): null|OldPassword
    {
        return OldPassword::find($id);
    }

    /**
     * Delete \App\Models\OldPassword record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        OldPassword::destroy($id);
    }

    /**
     * Create \App\Models\OldPassword record.
     *
     * @param array $arrayDetails
     * @return \App\Models\OldPassword
     */
    public function create(array $arrayDetails): OldPassword
    {
        return OldPassword::create($arrayDetails);
    }

    /**
     * Fetch or create a single \App\Models\OldPassword record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\OldPassword
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): OldPassword
    {
        return OldPassword::firstOrCreate($matchDetails, $arrayDetails);
    }

    /**
     * Update \App\Models\OldPassword record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int
    {
        return OldPassword::where('id', $id)->update($arrayDetails);
    }

    /**
     * Update \App\Models\OldPassword record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator
    {
        return OldPassword::paginate($pageSize);
    }
}

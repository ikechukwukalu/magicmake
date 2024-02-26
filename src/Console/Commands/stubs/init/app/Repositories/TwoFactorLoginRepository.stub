<?php

namespace App\Repositories;

use App\Contracts\TwoFactorLoginRepositoryInterface;
use App\Models\TwoFactorLogin;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class TwoFactorLoginRepository implements TwoFactorLoginRepositoryInterface
{

    /**
     * Fetch all \App\Models\TwoFactorLogin records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection
    {
        return TwoFactorLogin::all();
    }

    /**
     * Fetch \App\Models\TwoFactorLogin record by ID.
     *
     * @param int $id
     * @return \App\Models\TwoFactorLogin|null
     */
    public function getById(int $id): null|TwoFactorLogin
    {
        return TwoFactorLogin::find($id);
    }

    /**
     * Delete \App\Models\TwoFactorLogin record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        TwoFactorLogin::destroy($id);
    }

    /**
     * Create \App\Models\TwoFactorLogin record.
     *
     * @param array $arrayDetails
     * @return \App\Models\TwoFactorLogin
     */
    public function create(array $arrayDetails): TwoFactorLogin
    {
        return TwoFactorLogin::create($arrayDetails);
    }

    /**
     * Fetch or create a single \App\Models\TwoFactorLogin record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\TwoFactorLogin
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): TwoFactorLogin
    {
        return TwoFactorLogin::firstOrCreate($matchDetails, $arrayDetails);
    }

    /**
     * Update \App\Models\TwoFactorLogin record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int
    {
        return TwoFactorLogin::where('id', $id)->update($arrayDetails);
    }

    /**
     * Update \App\Models\TwoFactorLogin record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator
    {
        return TwoFactorLogin::paginate($pageSize);
    }
}

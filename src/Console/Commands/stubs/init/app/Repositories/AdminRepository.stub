<?php

namespace App\Repositories;

use App\Contracts\AdminRepositoryInterface;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminRepository implements AdminRepositoryInterface
{

    /**
     * Fetch all \App\Models\Admin records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection
    {
        return Admin::all();
    }

    /**
     * Fetch \App\Models\Admin record by ID.
     *
     * @param int $id
     * @return \App\Models\Admin|null
     */
    public function getById(int $id): null|Admin
    {
        return Admin::find($id);
    }

    /**
     * Delete \App\Models\Admin record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        Admin::destroy($id);
    }

    /**
     * Create \App\Models\Admin record.
     *
     * @param array $arrayDetails
     * @return \App\Models\Admin
     */
    public function create(array $arrayDetails): Admin
    {
        return Admin::create($arrayDetails);
    }

    /**
     * Fetch or create a single \App\Models\Admin record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\Admin
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): Admin
    {
        return Admin::firstOrCreate($matchDetails, $arrayDetails);
    }

    /**
     * Update \App\Models\Admin record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int
    {
        return Admin::where('id', $id)->update($arrayDetails);
    }

    /**
     * Update \App\Models\Admin record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator
    {
        return Admin::paginate($pageSize);
    }
}

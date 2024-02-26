<?php

namespace App\Repositories;

use App\Contracts\UserPasswordHolderRepositoryInterface;
use App\Models\User;
use App\Models\UserPasswordHolder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserPasswordHolderRepository implements UserPasswordHolderRepositoryInterface
{

    /**
     * Fetch all \App\Models\UserPasswordHolder records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection
    {
        return UserPasswordHolder::all();
    }

    /**
     * Fetch \App\Models\UserPasswordHolder record by ID.
     *
     * @param int $id
     * @return \App\Models\UserPasswordHolder|null
     */
    public function getById(int $id): null|UserPasswordHolder
    {
        return UserPasswordHolder::find($id);
    }

    /**
     * Delete \App\Models\UserPasswordHolder record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        UserPasswordHolder::destroy($id);
    }

    /**
     * Create \App\Models\UserPasswordHolder record.
     *
     * @param array $arrayDetails
     * @return \App\Models\UserPasswordHolder
     */
    public function create(array $arrayDetails): UserPasswordHolder
    {
        return UserPasswordHolder::create($arrayDetails);
    }

    /**
     * Fetch or create a single \App\Models\UserPasswordHolder record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\UserPasswordHolder
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): UserPasswordHolder
    {
        return UserPasswordHolder::firstOrCreate($matchDetails, $arrayDetails);
    }

    /**
     * Update \App\Models\UserPasswordHolder record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int
    {
        return UserPasswordHolder::where('id', $id)->update($arrayDetails);
    }

    /**
     * Update \App\Models\UserPasswordHolder record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator
    {
        return UserPasswordHolder::paginate($pageSize);
    }

    /**
     * Fetch \App\Models\UserPasswordHolder record by ID.
     *
     * @param \App\Models\User $user
     * @return \App\Models\UserPasswordHolder|null
     */
    public function belongsToUser(User $user): null|UserPasswordHolder
    {
        return UserPasswordHolder::whereBelongsTo($user)
                    ->first();
    }
}

<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{

    /**
     * Fetch all \App\Models\User records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection
    {
        return User::all();
    }

    /**
     * Fetch \App\Models\User record by ID.
     *
     * @param int $id
     * @return \App\Models\User|null
     */
    public function getById(int $id): null|User
    {
        return User::find($id);
    }

    /**
     * Delete \App\Models\User record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        User::destroy($id);
    }

    /**
     * Create \App\Models\User record.
     *
     * @param array $arrayDetails
     * @return \App\Models\User
     */
    public function create(array $arrayDetails): User
    {
        return User::create($arrayDetails);
    }

    /**
     * Fetch or create a single \App\Models\User record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\User
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): User
    {
        return User::firstOrCreate($matchDetails, $arrayDetails);
    }

    /**
     * Update \App\Models\User record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int
    {
        return User::where('id', $id)->update($arrayDetails);
    }

    /**
     * Update \App\Models\User record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator
    {
        return User::paginate($pageSize);
    }

    /**
     * Fetch \App\Models\User record(s) by email.
     *
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getByEmail(string $email): Builder
    {
        return User::where('email', $email);
    }

}

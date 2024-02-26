<?php

namespace App\Repositories;

use App\Contracts\ContactUsRepositoryInterface;
use App\Models\ContactUs;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactUsRepository implements ContactUsRepositoryInterface
{

    /**
     * Fetch all \App\Models\ContactUs records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection
    {
        return ContactUs::all();
    }

    /**
     * Fetch \App\Models\ContactUs record by ID.
     *
     * @param int $id
     * @return \App\Models\ContactUs|null
     */
    public function getById(int $id): null|ContactUs
    {
        return ContactUs::find($id);
    }

    /**
     * Delete \App\Models\ContactUs record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        ContactUs::destroy($id);
    }

    /**
     * Create \App\Models\ContactUs record.
     *
     * @param array $arrayDetails
     * @return \App\Models\ContactUs
     */
    public function create(array $arrayDetails): ContactUs
    {
        return ContactUs::create($arrayDetails);
    }

    /**
     * Fetch or create a single \App\Models\ContactUs record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\ContactUs
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): ContactUs
    {
        return ContactUs::firstOrCreate($matchDetails, $arrayDetails);
    }

    /**
     * Update \App\Models\ContactUs record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int
    {
        return ContactUs::where('id', $id)->update($arrayDetails);
    }

    /**
     * Update \App\Models\ContactUs record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator
    {
        return ContactUs::paginate($pageSize);
    }
}

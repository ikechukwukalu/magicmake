<?php

namespace App\Contracts;

use App\Models\ContactUs;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactUsRepositoryInterface
{

    /**
     * Fetch all \App\Models\ContactUs records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): EloquentCollection;

    /**
     * Fetch \App\Models\ContactUs record by ID.
     *
     * @param int $id
     * @return \App\Models\ContactUs|null
     */
    public function getById(int $id): null|ContactUs;

    /**
     * Delete \App\Models\ContactUs record by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * Create \App\Models\ContactUs record.
     *
     * @param array $arrayDetails
     * @return \App\Models\ContactUs
     */
    public function create(array $arrayDetails): ContactUs;

    /**
     * Fetch or create a single \App\Models\ContactUs record.
     *
     * @param array $matchDetails
     * @param array $arrayDetails
     * @return \App\Models\ContactUs
     */
    public function firstOrCreate(array $matchDetails, array $arrayDetails): ContactUs;

    /**
     * Update \App\Models\ContactUs record.
     *
     * @param int $id
     * @param array $arrayDetails
     * @return int
     */
    public function update(int $id, array $arrayDetails): int;

    /**
     * Update \App\Models\ContactUs record.
     *
     * @param int $pageSize
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $pageSize): LengthAwarePaginator;
}

<?php

namespace App\Services;

use App\Actions\ResponseData;
use App\Enums\UserModel;
use App\Facades\Database;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BasicCrudService
{

    /**
     * Create record.
     *
     * @param mixed $request
     * @param mixed $repository
     * @return array
     */
    protected function create(mixed $request, mixed $repository): ResponseData
    {
        $validated = $this->getValidatedValues($request);

        if ($model = $repository->create($validated)) {
            return responseData(true, Response::HTTP_OK, trans('crud.created'),
                    $model);
        };

        return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('crud.failed_create'));
    }

    /**
     * Update record.
     *
     * @param mixed $request
     * @param mixed $repository
     * @return array
     */
    protected function update(mixed $request, mixed $repository): ResponseData
    {
        $validated = $this->getValidatedValues($request);

        if (!$repository->getById($validated['id'])) {
            return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                        trans('crud.failed_update'));
        }

        if ($repository->update($validated['id'], $validated) > 0)
        {
            return responseData(true, Response::HTTP_OK, trans('crud.updated'),
                        $repository->getById($validated['id']));
        }

        return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('crud.failed_update'));
    }

    /**
     * Delete record.
     *
     * @param mixed $request
     * @param mixed $repository
     * @return array
     */
    protected function delete(mixed $request, mixed $repository): ResponseData
    {
        $validated = $this->getValidatedValues($request);

        if ($repository->getById($validated['id'])) {
            $repository->delete($validated['id']);

            return responseData(true, Response::HTTP_OK,
                        trans('crud.deleted'));
        }

        return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('crud.failed_delete'));
    }

    /**
     * Read records.
     *
     * @param mixed $repository
     * @param null|string|int $id
     * @return array
     */
    protected function read(mixed $repository, string $index, null|string|int $id = null): ResponseData
    {
        if (!isset($id)) {
            return responseData(true, Response::HTTP_OK, trans('crud.read'),
                    $repository->getPaginated(config("api.paginate.{$index}.pageSize")));
        }

        if ($id === 'all') {
            return responseData(true, Response::HTTP_OK, trans('crud.read'),
                    $repository->getAll());
        }

        return responseData(true, Response::HTTP_OK, trans('crud.read'),
                    $repository->getById($id));
    }

    /**
     * Read records.
     *
     * @param mixed $repository
     * @param null|string|int $id
     * @return array
     */
    protected function readByUserId(mixed $repository, string $index, string|int $userId, null|string|int $id = null): ResponseData
    {
        $user = Auth::user();

        if ($user->model == UserModel::CUSTOMER->value && $user->id != $userId)
        {
            return responseData(true, Response::HTTP_OK, trans('crud.read'), []);
        }

        if (!isset($id)) {
            return responseData(true, Response::HTTP_OK, trans('crud.read'),
                    $repository->getByUserIdPaginated($userId, config("api.paginate.{$index}.pageSize")));
        }

        if ($id === 'all') {
            return responseData(true, Response::HTTP_OK, trans('crud.read'),
                    $repository->getByUserId($userId));
        }

        return responseData(true, Response::HTTP_OK, trans('crud.read'),
                    $repository->getByUserIdAndId($userId, $id));
    }

    /**
     * Create record with DB facade.
     *
     * @param mixed $request
     * @param string $table
     * @return array
     */
    protected function createWithDB(mixed $request, string $table): ResponseData
    {
        $validated = $this->getValidatedValues($request);

        if ($id = Database::create($validated, $table)) {
            $model = Database::getById($id, $table);
            return responseData(true, Response::HTTP_OK, trans('crud.created'), $model);
        };

        return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('crud.failed_create'));
    }

    /**
     * Update record with DB facade.
     *
     * @param mixed $request
     * @param string $table
     * @return array
     */
    protected function updateWithDB(mixed $request, string $table): ResponseData
    {
        $validated = $this->getValidatedValues($request);

        if (!Database::getById($validated['id'], $table)) {
            return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                        trans('crud.failed_update'));
        }

        if (Database::update($validated['id'], $validated, $table) > 0)
        {
            $model = Database::getById($validated['id'], $table);
            return responseData(true, Response::HTTP_OK, trans('crud.updated'),$model);
        }

        return responseData(false, Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('crud.failed_update'));
    }

    /**
     * @param mixed $request
     * @return array
     */
    protected function getValidatedValues(mixed $request): array
    {
        if (is_array($request)) {
            $validated = $request;
        } else {
            $validated = $request->validated();
        }

        return $validated;
    }
}

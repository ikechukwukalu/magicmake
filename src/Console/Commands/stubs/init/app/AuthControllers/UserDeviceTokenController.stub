<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserDeviceTokenCreateRequest;
use App\Http\Requests\Auth\UserDeviceTokenDeleteRequest;
use App\Http\Requests\Auth\UserDeviceTokenReadRequest;
use App\Http\Requests\Auth\UserDeviceTokenUpdateRequest;
use App\Services\Auth\UserDeviceTokenService;
use Illuminate\Http\JsonResponse;

class UserDeviceTokenController extends Controller
{

    public function __construct(private UserDeviceTokenService $userDeviceTokenService)
    { }

    /**
     * Create user device token.
     *
     * @header Authorization Bearer {Your key}
     *
     * @bodyParam device_token string required The name of the user device token. Example: Sample 333445-123234455-2234555-22223edde
     *
     * @response 200
     *
     * {
     * "success": true,
     * "status_code": 200,
     * "message": string
     * "data": {}
     * }
     *
     * @authenticated
     * @subgroup User Device Token APIs
     * @group Auth APIs
     */
    public function create(UserDeviceTokenCreateRequest $request): JsonResponse
    {
        return $this->_create($request, $this->userDeviceTokenService);
    }

    /**
     * Update user device token.
     *
     * @header Authorization Bearer {Your key}
     *
     * @bodyParam id string required The id of the user device token. Example: 1
     * @bodyParam active string required Whether or not this user device token will be displayed. 0 for no and 1 yes. Example: 0
     *
     * @response 200
     *
     * {
     * "success": true,
     * "status_code": 200,
     * "message": string
     * "data": {}
     * }
     *
     * @authenticated
     * @subgroup User Device Token APIs
     * @group Auth APIs
     */
    public function update(UserDeviceTokenUpdateRequest $request): JsonResponse
    {
        return $this->_update($request, $this->userDeviceTokenService);
    }

    /**
     * Delete user device token.
     *
     * @header Authorization Bearer {Your key}
     *
     * @bodyParam id string required The id of the user device token. Example: 1
     *
     * @response 200
     *
     * {
     * "success": true,
     * "status_code": 200,
     * "message": string
     * "data": {}
     * }
     *
     * @authenticated
     * @subgroup User Device Token APIs
     * @group Auth APIs
     */
    public function delete(UserDeviceTokenDeleteRequest $request): JsonResponse
    {
        return $this->_delete($request, $this->userDeviceTokenService);
    }

    /**
     * Read user device token.
     *
     * Fetch a record or records from the user device tokens table.
     * The <b>id</b> param is optional but can either be a <b>string|null|int</b>
     *
     * If the <b>id</b> has a <b>null</b> value the records will be paginated.
     * The returned page size is be set from <b>api.paginate.user_address.pageSize</b>
     * config.
     *
     * If the <b>id</b> is a <b>string</b> value it can only be set as <b>'all'</b>.
     * This will return all the records without being paginated.
     *
     * If the <b>id</b> value is an <b>integer</b> it will try to fetch a single
     * matching record.
     *
     * @header Authorization Bearer {Your key}
     *
     * @urlParam id string The ID of the record. Example: all
     *
     * @response 200
     *
     * {
     * "success": true,
     * "status_code": 200,
     * "message": string
     * "data": {}
     * }
     *
     * @authenticated
     * @subgroup User Device Token APIs
     * @group Auth APIs
     */
    public function read(UserDeviceTokenReadRequest $request, null|string|int $id = null): JsonResponse
    {
        return $this->_read($this->userDeviceTokenService, $id);
    }

}

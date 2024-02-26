<?php

namespace App\Services\Auth;

use App\Actions\ResponseData;
use App\Contracts\UserDeviceTokenRepositoryInterface;
use App\Http\Requests\Auth\UserDeviceTokenCreateRequest;
use App\Http\Requests\Auth\UserDeviceTokenDeleteRequest;
use App\Http\Requests\Auth\UserDeviceTokenUpdateRequest;
use App\Services\BasicCrudService;
use Illuminate\Support\Facades\Auth;

class UserDeviceTokenService extends BasicCrudService
{

    public function __construct(private UserDeviceTokenRepositoryInterface $userDeviceTokenRepository)
    { }

    /**
     * Handle the create request.
     *
     * @param  \App\Http\Requests\UserDeviceTokenCreateRequest $request
     * @return \App\Actions\ResponseData
     */
    public function handleCreate(UserDeviceTokenCreateRequest $request): ResponseData
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::user()->id;

        return $this->create($validated, $this->userDeviceTokenRepository);
    }

    /**
     * Handle the update request.
     *
     * @param  \App\Http\Requests\UserDeviceTokenUpdateRequest $request
     * @return \App\Actions\ResponseData
     */
    public function handleUpdate(UserDeviceTokenUpdateRequest $request): ResponseData
    {
        return $this->update($request, $this->userDeviceTokenRepository);
    }

    /**
     * Handle the delete request.
     *
     * @param  \App\Http\Requests\UserDeviceTokenDeleteRequest $request
     * @return \App\Actions\ResponseData
     */
    public function handleDelete(UserDeviceTokenDeleteRequest $request): ResponseData
    {
        return $this->delete($request, $this->userDeviceTokenRepository);
    }

    /**
     * Handle the read request.
     *
     * @param null|string|int $id
     * @return array
     */
    public function handleRead(null|string|int $id = null): ResponseData
    {
        return $this->read($this->userDeviceTokenRepository, 'user_device_token', $id);
    }

}

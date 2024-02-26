<?php

namespace App\Services\Auth;

use App\Actions\ResponseData;
use App\Contracts\ContactUsRepositoryInterface;
use App\Http\Requests\Auth\ContactUsCreateRequest;
use App\Http\Requests\Auth\ContactUsDeleteRequest;
use App\Http\Requests\Auth\ContactUsUpdateRequest;
use App\Services\BasicCrudService;
use Illuminate\Support\Facades\Auth;

class ContactUsService extends BasicCrudService
{
    public function __construct(private ContactUsRepositoryInterface $contactUsRepository)
    { }

    /**
     * Handle the request.
     *
     * @param  ContactUsCreateRequest  $request
     * @return ResponseData
     */
    public function handleCreate(ContactUsCreateRequest $request): ResponseData
    {
        $validated = $request->validated();

        if (Auth::check()) {
            $validated['user_id'] = Auth::user()->id;
        }

        return $this->create($validated, $this->contactUsRepository);
    }

    /**
     * Update ContactUs.
     *
     * @param ContactUsUpdateRequest $request
     * @return ResponseData
     */
    public function handleUpdate(ContactUsUpdateRequest $request): ResponseData
    {

        return $this->update($request->validated(), $this->contactUsRepository);

    }

    /**
     * Delete ContactUs.
     *
     * @param ContactUsDeleteRequest $request
     * @return ResponseData
     */
    public function handleDelete(ContactUsDeleteRequest $request): ResponseData
    {
        return $this->delete($request, $this->contactUsRepository);
    }

    /**
     * Read ContactUs.
     *
     * @param null|string|int $id
     * @return ResponseData
     */
    public function handleRead(null|string|int $id = null): ResponseData
    {
        return $this->read($this->contactUsRepository, 'contact_us', $id);
    }
}

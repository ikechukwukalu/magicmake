<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ContactUsCreateRequest;
use App\Http\Requests\Auth\ContactUsDeleteRequest;
use App\Http\Requests\Auth\ContactUsReadRequest;
use App\Http\Requests\Auth\ContactUsUpdateRequest;
use App\Services\Auth\ContactUsService;
use Illuminate\Http\JsonResponse;

class ContactUsController extends Controller
{
    public function __construct(private ContactUsService $contactUsService)
    { }

    /**
     * Create contact us message.
     *
     * @header Authorization Bearer {Your key}
     *
     * @bodyParam reason string required The reason for contacting us. Example: I want to make an enquiry
     * @bodyParam subject string required The subject of the contact us message. Example: Discount for multiple export
     * @bodyParam message string required The message for customer support . Example: How do you give discount for large shipments
     * @bodyParam type string required This is the type of enquiry. Can either be complaint, enquiry or support. Example: inquiry
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
     * @subgroup Contact Us APIs
     * @group No Auth APIs
     */
    public function create(ContactUsCreateRequest $request): JsonResponse
    {
        return $this->_create($request, $this->contactUsService);
    }

    /**
     * Update contact us message.
     *
     * @header Authorization Bearer {Your key}
     *
     * @bodyParam id string required The id of the contact us message. Example: 1
     * @bodyParam reason string required The reason for contacting us. Example: I want to make an enquiry
     * @bodyParam message string Contact us message. Example: How do you do give discounts
     * @bodyParam type string required This is the type of enquiry. Can either be complaint, enquiry or support. Example: inquiry
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
     * @subgroup Contact Us APIs
     * @group Auth APIs
     */
    public function update(ContactUsUpdateRequest $request): JsonResponse
    {
        return $this->_update($request, $this->contactUsService);
    }

    /**
     * Delete contact us message.
     *
     * @header Authorization Bearer {Your key}
     *
     * @bodyParam id string required The id of the contact us message. Example: 1
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
     * @subgroup Contact Us APIs
     * @group Auth APIs
     */
    public function delete(ContactUsDeleteRequest $request): JsonResponse
    {
        return $this->_delete($request, $this->contactUsService);
    }

    /**
     * Read contact us message.
     *
     * Fetch a record or records from the contact us messages table.
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
     * @subgroup Contact Us APIs
     * @group Auth APIs
     */
    public function read(ContactUsReadRequest $request, null|string|int $id = null): JsonResponse
    {
        return $this->_read($this->contactUsService, $id);
    }
}

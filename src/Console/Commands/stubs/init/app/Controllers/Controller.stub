<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function _create(mixed $request, mixed $service, string|null $component = null): JsonResponse|View
    {
        if ($data = $service->handleCreate($request)) {
            return httpResponseFormat($request, $data, $component);
        };

        return unknownErrorResponseFormat($request);
    }

    protected function _update(mixed $request, mixed $service, string|null $component = null): JsonResponse|View
    {
        if ($data = $service->handleUpdate($request)) {
            return httpResponseFormat($request, $data, $component);
        };

        return unknownErrorResponseFormat($request);
    }

    protected function _delete(mixed $request, mixed $service, string|null $component = null): JsonResponse|View
    {
        if ($data = $service->handleDelete($request)) {
            return httpResponseFormat($request, $data, $component);
        };

        return unknownErrorResponseFormat($request);
    }

    protected function _read(mixed $request, mixed $service, null|string|int $id = null, string|null $component = null): JsonResponse|View
    {
        if ($data = $service->handleRead($id)) {
            return httpResponseFormat($request, $data, $component);
        };

        return unknownErrorResponseFormat($request);
    }
}

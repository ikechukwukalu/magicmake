<?php

use App\Actions\AdvancedSearch;
use App\Actions\ResponseData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as RouteResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

if (!function_exists('responseData')) {
    /**
     * @param string $status
     * @param int $status_code
     * @param string $message
     * @return null|object|array $data
     */
    function responseData(bool $success, int $status_code, string $message, null|object|array $data = []): ResponseData
    {
        return new ResponseData($success, $status_code, $message, $data);
    }
}

if (!function_exists('shouldResponseBeJson')) {
    /**
     * @param mixed $request
     * @return bool
     */
    function shouldResponseBeJson(mixed $request): bool
    {
        if ($request?->wantsJson()) {
            return true;
        }
        if ($request?->ajax()) {
            return true;
        }

        return false;
    }
}

if (!function_exists('httpJsonResponse')) {
    /**
     * @param ResponseData $response
     * @return \Illuminate\Http\JsonResponse
     */
    function httpJsonResponse(ResponseData $response): JsonResponse
    {
        return response()->json($response->toArray(), $response->status_code);
    }
}

if (!function_exists('httpViewResponse')) {
    /**
     * @param ResponseData $response
     * @param string $component
     * @return \Illuminate\Contracts\View\View
     */
    function httpViewResponse(ResponseData $response, string $component): View
    {
        return view($component, $response->toArray());
    }
}

if (!function_exists('httpResponseFormat')) {
    /**
     * @param ResponseData $response
     * @param string|null $component
     * @return \Illuminate\Contracts\View\View
     */
    function httpResponseFormat(mixed $request, ResponseData $response, string|null $component = null): JsonResponse|View
    {
        if (!$component) {
            return httpJsonResponse($response);
        }

        if (shouldResponseBeJson($request)) {
            return httpJsonResponse($response);
        }

        return httpViewResponse($response, $component);
    }
}

if (!function_exists('unknownErrorJsonResponse')) {
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    function unknownErrorJsonResponse(): JsonResponse
    {
        $message = trans('general.unknown_error');

        return httpJsonResponse(responseData(false, 400, $message));
    }
}

if (!function_exists('unknownErrorViewResponse')) {
    /**
     * @return \Illuminate\Contracts\View\View
     */
    function unknownErrorViewResponse(): View
    {
        $message = trans('general.unknown_error');

        return httpViewResponse(responseData(false, 400, $message), 'errors/500');
    }
}

if (!function_exists('unknownErrorResponseFormat')) {
    /**
     * @param ResponseData $response
     * @param string|null $component
     * @return \Illuminate\Contracts\View\View
     */
    function unknownErrorResponseFormat(mixed $request): JsonResponse|View
    {
        $message = trans('general.unknown_error');
        $response = responseData(false, 400, $message);

        if (shouldResponseBeJson($request)) {
            return httpJsonResponse($response);
        }

        return httpViewResponse($response, 'errors/500');
    }
}

if (!function_exists('diffForHumans')) {
    /**
     * @param string $date
     * @return bool
     */
    function diffForHumans(string $date): string
    {
        return Carbon::parse($date)->diffForHumans();
    }
}

if (!function_exists('dispatchRequest')) {
    /**
     * @param string $url
     * @param array $params
     * @param string $method
     * @return JsonResponse|RouteResponse
     */
    function dispatchRequest(string $url, array $params = [], string $method = 'POST'): JsonResponse|RouteResponse
    {
        $request = Request::create($url, $method, $params);

        $request->headers->set('Accept', 'application/json');

        return Route::dispatch($request);
    }
}

if (!function_exists('tryCatch')) {
    /**
     * @param callable $tryFunc
     * @param callable $catchFunc
     * @return mixed
     */

    function tryCatch(callable $tryFunc, callable $catchFunc): mixed
    {
        try {
            return $tryFunc();
        } catch (\Throwable $th) {
            appLog($th->getMessage());
            return $catchFunc($th);
        }
    }
}

if (!function_exists('appLog')) {
    /**
     * @param mixed $message
     * @return void
     */
    function appLog (mixed $message): void
    {
        Log::error($message);
    }
}

if (!function_exists('sanitizeJsonForUTF8')) {
    /**
     * Summary of sanitizeJsonForUTF8
     * @param array $body
     * @return array
     */
    function sanitizeJsonForUTF8(array $body): array
    {
        // $body = mb_convert_encoding($body, 'UTF-8', 'UTF-8');
        return (array) json_decode(json_encode($body, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}

if (!function_exists('pageSize')) {
    /**
     * Page size function
     *
     * @param integer $pageSize
     * @return integer
     */
    function pageSize(int $pageSize): int
    {
        $size = request()->query('size', $pageSize);
        if (!is_numeric($size)) {
            return $pageSize;
        }

        $size = (int)$size;
        if ($size > 30) {
            return $pageSize;
        }

        return $size;
    }
}

if (!function_exists('advancedSearch')) {
    /**
     * Summary of advancedSearch
     * @param string $relationship
     * @param string $foreignKeyString
     * @param string|array $column
     * @param string|null $parent
     * @return App\Actions\AdvancedSearch
     */
    function advancedSearch(string $relationship, string $foreignKeyString,
        string | array $column, string|null $parent = null): AdvancedSearch {
        return new AdvancedSearch($relationship, $foreignKeyString, $column, $parent);
    }
}

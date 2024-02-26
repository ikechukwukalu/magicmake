<?php

use App\Actions\ResponseData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as RouteResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

if( !function_exists('responseData') )
{
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

if ( !function_exists('shouldResponseBeJson') )
{
    /**
     * @param mixed $request
     * @return bool
     */
    function shouldResponseBeJson(mixed $request): bool
    {
        return $request->wantsJson() || $request->ajax();
    }
}

if( !function_exists('httpJsonResponse') )
{
    /**
     * @param ResponseData $response
     * @return \Illuminate\Http\JsonResponse
     */
    function httpJsonResponse(ResponseData $response): JsonResponse
    {
        return response()->json($response->toArray(), $response->status_code);
    }
}

if( !function_exists('unknownErrorJsonResponse') )
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    function unknownErrorJsonResponse(): JsonResponse
    {
        $message = trans('general.unknown_error');

        return httpJsonResponse(responseData(false, 422, $message));
    }
}

if ( !function_exists('diffForHumans') )
{
    /**
     * @param string $date
     * @return bool
     */
    function diffForHumans(string $date): string
    {
        return Carbon::parse($date)->diffForHumans();
    }
}

if ( !function_exists('dispatchRequest') )
{
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

if (!function_exists('errorLog')) {
    /**
     * @param mixed $message
     * @return void
     */
    function appLog (mixed $message): void
    {
        Log::error($message);
    }
}

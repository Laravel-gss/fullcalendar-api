<?php

namespace App\Utils\Api;

use App\Http\Resources\Api\ErrorResource;
use App\Http\Resources\Api\SuccessResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CommonUtil
{
    /**
     * Create a success response with optional message.
     *
     * @param  array  $data
     * @param  string|null  $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function successResponse($data, $message = null, $status_code = Response::HTTP_OK): JsonResponse
    {
        $resource = new SuccessResource($data ?? []);
        return self::addMessageAndStatus($resource, $message, $status_code, false);
    }

    /**
     * Create an error response with a given message and status code.
     *
     * @param  string  $message
     * @param  int  $status_code
     * @return \Illuminate\Http\JsonResponse
    */
    public static function errorResponse($message, $status_code, $errors = []): JsonResponse
    {
        $resource = new ErrorResource(['data' => null, 'errors' => $errors]);
        return self::addMessageAndStatus($resource, $message, $status_code);
    }

    private static function addMessageAndStatus($resource, $message = null, $status_code, $merge_status_code = true): JsonResponse
    {
        $additional = [];

        if (!empty($message)) {
            $additional['message'] = $message;
        }

        if($merge_status_code) {
            $additional['status'] = $status_code;
        }

        $resource->additional($additional);

        return $resource->response()->setStatusCode($status_code);
    }

}

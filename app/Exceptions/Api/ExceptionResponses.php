<?php

namespace App\Exceptions\Api;

use Throwable;

class ExceptionResponses
{
    public static function getResponseForException(Throwable $e): ?array
    {
        $exception_responses = config('exception_responses');

        foreach ($exception_responses as $exception_class => $response) {
            if ($e instanceof $exception_class) {

                if(!isset($response['message'])) {
                    $response['message'] = $e->getMessage();
                }

                $response['errors'] = method_exists($e, 'errors') ? $e->errors() : [];

                return $response;
            }
        }
        return null;
    }

}

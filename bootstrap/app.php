<?php

use App\Exceptions\Api\ExceptionResponses;
use App\Http\Middleware\Api\Timezone;
use App\Utils\Api\CommonUtil;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            Timezone::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e) {

            $response = ExceptionResponses::getResponseForException($e);

            if ($response) {
                return CommonUtil::errorResponse($response['message'], $response['status'], $response['errors']);
            }
        });
    })->create();

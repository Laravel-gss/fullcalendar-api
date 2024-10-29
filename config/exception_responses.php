<?php

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return [
    UnauthorizedHttpException::class => [
        'status' => \Illuminate\Http\Response::HTTP_UNAUTHORIZED
    ],
    NotFoundHttpException::class => [
        'status' => \Illuminate\Http\Response::HTTP_NOT_FOUND,
        'message' => 'Route not found.',
    ],
    MethodNotAllowedHttpException::class => [
        'status' => \Illuminate\Http\Response::HTTP_METHOD_NOT_ALLOWED,
        'message' => 'Method not allowed.',
    ],
    ValidationException::class => [
        'status' => \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
    ],
];

<?php

namespace Demoshop\Local\Infrastructure\Error;

use Demoshop\Local\Infrastructure\Error\exceptions\concreteExceptions\InvalidCredentialsException;
use Demoshop\Local\Infrastructure\Error\exceptions\concreteExceptions\UnauthorizedException;
use Demoshop\Local\Infrastructure\Error\exceptions\IException;
use Demoshop\Local\Infrastructure\http\ErrorResponse;
use Demoshop\Local\Infrastructure\http\RedirectResponse;

class ErrorHandler
{
    public static function handle(\Throwable $exception): void
    {
        $statusCode = $exception->getCode() ?: 500;
        $message = $exception->getMessage();

        switch (true) {
            case $exception instanceof UnauthorizedException:
                $redirectUrl = "/loginPage?message=" . urlencode('Unauthorized, please log in');
                $response = new RedirectResponse($redirectUrl);
                break;

            case $exception instanceof InvalidCredentialsException:
                $redirectUrl = "/loginPage?message=" . urlencode('Incorrect credentials.');
                $response = new RedirectResponse($redirectUrl);
                break;

            case $exception instanceof IException:
                $response = new ErrorResponse('/error?msg=' . urlencode($message), $statusCode);
                break;

            default:
                $response = new ErrorResponse('/error?msg=internal_server_error', 500);
        }

        $response->send();
        exit;
    }
}
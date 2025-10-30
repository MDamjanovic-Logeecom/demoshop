<?php

namespace Demoshop\Local\Presentation\controllers;

use Demoshop\Local\Business\Interfaces\Service\IUserService;
use Demoshop\Local\Infrastructure\Error\exceptions\concreteExceptions\InvalidCredentialsException;
use Demoshop\Local\Infrastructure\http\ErrorResponse;
use Demoshop\Local\Infrastructure\http\HtmlResponse;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Infrastructure\http\RedirectResponse;

/**
 * Class UserController
 *
 * Handles HTTP requests related to users.
 * Uses UserService for business logic and returns HttpResponse objects.
 */
class UserController
{
    /**
     * @var IUserService Service layer for user-related operations.
     *  Concrete instance is injected in the constructor.
     */
    private IUserService $service;

    public function __construct(IUserService $service)
    {
        $this->service = $service;
    }

    /**
     * Register a new user
     *
     * @param HttpRequest $request
     *
     * @return RedirectResponse|ErrorResponse
     */
    public function register(HttpRequest $request): RedirectResponse|ErrorResponse
    {
        $headerKey = $request->getServer('REGISTRATION_KEY', '');
        $expectedKey = $_ENV['REGISTRATION_KEY'] ?? '';

        if ($headerKey !== $expectedKey) {
            return new ErrorResponse('/error?msg=invalid_registration_key', 403);
        }

        $username = $request->getHttpPost('username');
        $password = $request->getHttpPost('password');

        try {
            $user = $this->service->register($username, $password);

            if ($user) {
                return new RedirectResponse('/success');
            }

            return new ErrorResponse('/error?msg=registration_failed', 400);

        } catch (\InvalidArgumentException $invalidArgumentException) {
            return new ErrorResponse('/error?msg=' . urlencode($invalidArgumentException->getMessage()), 400);
        }
    }

    /**
     * Check if user credentials exist, keep logged in if field checked.
     *
     * @param HttpRequest $request
     *
     * @return RedirectResponse
     *
     * @throws InvalidCredentialsException
     */
    public function login(HttpRequest $request): RedirectResponse
    {
        $username = $request->getHttpPost('username');
        $password = $request->getHttpPost('password');
        $rememberMe = (bool)$request->getHttpPost('remember_me');

        $user = $this->service->login($username, $password, $rememberMe);

        if (!$user) {
            throw new InvalidCredentialsException();
        }

        return new RedirectResponse('/admin');
    }

    /**
     * Get the login page
     *
     * @param HttpRequest $request
     *
     * @return HtmlResponse|RedirectResponse
     */
    public function showLoginPage(HttpRequest $request): HtmlResponse|RedirectResponse
    {
        if ($this->service->isLoggedIn()) {
            return new RedirectResponse('/admin');
        }

        $message = $request->getHttpGet('message', '');

        return new HtmlResponse('login.php', ['message' => $message]);
    }
}

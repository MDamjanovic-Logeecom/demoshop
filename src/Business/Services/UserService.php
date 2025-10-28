<?php

namespace Demoshop\Local\Business\Services;

use Demoshop\Local\Business\Interfaces\Repository\IUserRepository;
use Demoshop\Local\Business\Interfaces\Service\IUserService;
use Demoshop\Local\Business\Validation\UserValidator;
use Demoshop\Local\DTO\UserDTO;
use Demoshop\Local\Presentation\helper\SessionManager;

/**
 *  Class UserService
 *
 *  Service layer for user operations.
 *  Responsible for orchestrating user-related business logic
 *  and delegating operations to the UserRepository.
 *  Implements the IUserService interface.
 */
class UserService implements IUserService
{
    /**
     * @var IUserRepository user repository instance
     */
    private IUserRepository $repository;
    /**
     * @var UserValidator validator of user operations
     */
    private UserValidator $validator;

    public function __construct(IUserRepository $repository, UserValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Registers a new user
     *
     * @param string $username
     * @param string $password
     *
     * @return UserDTO|null
     */
    public function register(string $username, string $password): ?UserDTO
    {
        $dto = $this->repository->getUserByUsername($username);
        $this->validator->validateRegistration($username, $password, $dto);

        return $this->repository->register($username, $password);
    }

    /**
     * Checks if user credentials exist
     *
     * @param string $username
     * @param string $password
     * @param bool $rememberMe
     *
     * @return UserDTO|null
     */
    public function login(string $username, string $password, bool $rememberMe = false): ?UserDTO
    {
        $user = $this->repository->getUserByUsername($username);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user->password)) {
            return null;
        }

        $sessionManager = SessionManager::getInstance();
        $sessionManager->setAdminId($user->id);

        if ($rememberMe) {
            $sessionManager->setRememberMeCookie($user->id);
        }

        return $user;
    }

    /**
     * Checks if user is logged-in already
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        $sessionManager = SessionManager::getInstance();

        return $sessionManager->isLoggedIn();
    }
}

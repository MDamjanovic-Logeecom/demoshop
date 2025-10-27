<?php

namespace Demoshop\Local\Business;

use Demoshop\Local\Business\Validation\UserValidator;
use Demoshop\Local\Data\IUserRepository;
use Demoshop\Local\DTO\UserDTO;

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
        $this->validator->validateRegistration($username, $password);

        return $this->repository->register($username, $password);
    }

    /**
     * Checks if user credentials exist
     *
     * @param string $username
     * @param string $password
     *
     * @return UserDTO|null
     */
    public function login(string $username, string $password): ?UserDTO
    {
        $user = $this->repository->getUserByUsername($username);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user->password)) {
            return null;
        }

        return $user;
    }
}

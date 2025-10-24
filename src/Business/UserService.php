<?php

namespace Demoshop\Local\Business;

use Demoshop\Local\Data\IUserRepository;
use Demoshop\Local\DTO\UserDTO;

class UserService implements IUserService
{
    private IUserRepository $repository;

    public function __construct(IUserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function register(string $username, string $password): ?UserDTO
    {
        return $this->repository->register($username, $password);
    }

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

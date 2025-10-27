<?php

namespace Demoshop\Local\Business\Interfaces\Service;

use Demoshop\Local\DTO\UserDTO;

interface IUserService
{
    public function register(string $username, string $password): ?UserDTO;
    public function login(string $username, string $password): ?UserDTO;
}

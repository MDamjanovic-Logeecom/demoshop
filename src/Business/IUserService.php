<?php

namespace Demoshop\Local\Business;

use Demoshop\Local\DTO\UserDTO;

interface IUserService
{
    public function register(string $username, string $password): ?UserDTO;
    public function login(string $username, string $password): ?UserDTO;
}

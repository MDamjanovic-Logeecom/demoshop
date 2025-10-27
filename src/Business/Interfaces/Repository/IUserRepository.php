<?php

namespace Demoshop\Local\Business\Interfaces\Repository;

use Demoshop\Local\DTO\UserDTO;

interface IUserRepository
{
    public function register(string $username, string $password): ?UserDTO;
    public function getUserByUsername(string $username): ?UserDTO;

}
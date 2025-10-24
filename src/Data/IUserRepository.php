<?php

namespace Demoshop\Local\Data;

use Demoshop\Local\DTO\UserDTO;

interface IUserRepository
{
    public function register(string $username, string $password): ?UserDTO;
    public function getUserByUsername(string $username): ?UserDTO;

}
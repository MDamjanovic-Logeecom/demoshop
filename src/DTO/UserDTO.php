<?php

namespace Demoshop\Local\DTO;

class UserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $password,
    ) {
    }
}
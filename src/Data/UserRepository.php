<?php

namespace Demoshop\Local\Data;

use Demoshop\Local\Data\Models\EloquentUser;
use Demoshop\Local\DTO\UserDTO;

class UserRepository implements IUserRepository
{
    public function register(string $username, string $password): ?UserDTO
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $eloquentUser = new EloquentUser([
            'username' => $username,
            'password' => $hashedPassword,
        ]);

        if ($eloquentUser->save()) {
            return $this->mapEloquentToDTO($eloquentUser);
        }

        return null;
    }

    public function getUserByUsername(string $username): ?UserDTO
    {
        $eloquentUser = EloquentUser::where('username', $username)->first();

        if (!$eloquentUser) {
            return null;
        }

        return $this->mapEloquentToDTO($eloquentUser);
    }

    private function mapEloquentToDTO(EloquentUser $eloquentUser): UserDTO
    {
        return new UserDTO(
            id: $eloquentUser->id,
            username: $eloquentUser->username,
            password: $eloquentUser->password,
        );
    }
}
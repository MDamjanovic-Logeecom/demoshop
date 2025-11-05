<?php

namespace Demoshop\Local\Data\Repositories;

use Demoshop\Local\Business\Interfaces\Repository\IUserRepository;
use Demoshop\Local\Data\Models\EloquentUser;
use Demoshop\Local\DTO\UserDTO;

/**
 * Repository for handling Users
 */
class UserRepository implements IUserRepository
{
    /**
     * Register a new user with username and password
     *
     * @param string $username
     * @param string $password
     *
     * @return UserDTO|null
     */
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

    /**
     * Get a user by their username
     *
     * @param string $username
     *
     * @return UserDTO|null
     */
    public function getUserByUsername(string $username): ?UserDTO
    {
        $eloquentUser = EloquentUser::where('username', $username)->first();

        if (!$eloquentUser) {
            return null;
        }

        return $this->mapEloquentToDTO($eloquentUser);
    }

    /**
     * Map eloquent model to data transfer object
     *
     * @param EloquentUser $eloquentUser
     *
     * @return UserDTO
     */
    private function mapEloquentToDTO(EloquentUser $eloquentUser): UserDTO
    {
        return new UserDTO(
            id: $eloquentUser->id,
            username: $eloquentUser->username,
            password: $eloquentUser->password,
        );
    }
}
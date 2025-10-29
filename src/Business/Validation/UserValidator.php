<?php

namespace Demoshop\Local\Business\Validation;

use Demoshop\Local\DTO\UserDTO;
use Demoshop\Local\Infrastructure\Error\exceptions\concreteExceptions\InvalidArgumentsException;
use InvalidArgumentException;

/**
 * Validator class to verify user actions
 */
class UserValidator
{
    /**
     * Throws exceptions if something goes wrong with the validation
     *
     * @param string $username
     * @param string $password
     * @param UserDTO|null $userDTO
     *
     * @return void
     */
    public function validateRegistration(string $username, string $password, ?UserDTO $userDTO): void
    {
        if (empty($username)) {
            throw new InvalidArgumentsException('Username cannot be empty.');
        }

        if (empty($password)) {
            throw new InvalidArgumentsException('Password cannot be empty.');
        }

        if (!empty($userDTO)) {
            throw new InvalidArgumentsException('User with the same username already exists.');
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentsException('Password must be at least 8 characters long.');
        }

        if (
            !preg_match('/[A-Z]/', $password) || // Uppercase
            !preg_match('/[a-z]/', $password) || // Lowercase
            !preg_match('/\d/', $password) ||    // Number
            !preg_match('/[\W_]/', $password)    // Special character
        ) {
            throw new InvalidArgumentsException(
                'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.'
            );
        }
    }
}

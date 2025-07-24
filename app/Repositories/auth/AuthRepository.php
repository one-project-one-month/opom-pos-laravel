<?php

namespace App\Repositories\Auth;

use App\Models\User;

class AuthRepository
{
    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */

    public function createUser($data): User
    {
        $user = User::create([
            "name" => $data["name"],
            "email" => $data["email"],
            "password" => $data["password"],
        ]);

        return $user;
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail($data): ?User
    {
        $user = User::where('email', $data["email"])->first();

        if (!$user || !password_verify($data["password"], $user->password)) {
            return null;
        }

        return $user;
    }
}

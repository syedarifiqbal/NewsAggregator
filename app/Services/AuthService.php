<?php

namespace App\Services;

use App\Contracts\UserRepositoryContract;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private UserRepositoryContract $userRepo) {}

    public function register(array $data): string
    {
        $user = $this->userRepo->create($data);

        return $user->createToken('auth-token')->plainTextToken;
    }

    public function login(array $credentials): string
    {
        $user = $this->userRepo->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken('auth-token')->plainTextToken;
    }
}

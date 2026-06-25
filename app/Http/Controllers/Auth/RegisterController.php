<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;

class RegisterController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function __invoke(RegisterRequest $request)
    {
        $token = $this->authService->register($request->validated());

        return $this->success(['token' => $token], 'Registration successful.', 201);
    }
}

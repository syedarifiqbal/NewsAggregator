<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;

class LoginController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function __invoke(LoginRequest $request)
    {
        $token = $this->authService->login($request->validated());

        return $this->success(['token' => $token], 'Login successful.');
    }
}

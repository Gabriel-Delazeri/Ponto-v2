<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(LoginRequest $loginRequest)
    {
        $token = $this->authService->loginGetToken($loginRequest->validated());

        return $this->responseAuthUser($token);
    }

    public function logout() {
        $this->authService->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh() {
        return $this->responseAuthUser($this->authService->getRefresh());
    }

    protected function responseAuthUser($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->authService->getTimeToLive(),
            'user' => $this->authService->getUser()
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use Illuminante\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Providers\AuthServiceProvider;

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

        return $this->createNewToken($token);
    }

    public function logout() {
        $this->authService->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh() {
        return $this->createNewToken($this->authService->getRefresh());
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->authService->getTimeToLive(),
            'user' => $this->authService->getUser()
        ]);
    }
}

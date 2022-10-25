<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;

class AuthService
{
    CONST MULTIPLY_TIME_TO_LEAVE = 60;
    public function loginGetToken($credentials)
    {
        $token = auth()->attempt($credentials);

        if (! $token) {
            throw new HttpResponseException(response()->json([
                'error' => 'Unauthorized'
            ], 401));
        }

        return $token;
    }

    public function logout()
    {
        auth()->logout();
    }

    public function getRefresh()
    {
        return auth()->refresh();
    }

    public function getUser()
    {
        return auth()->user();
    }

    public function getTimeToLive()
    {
        $timeToLive = auth()->factory()->getTTL() * Self::MULTIPLY_TIME_TO_LEAVE;
        return $timeToLive;
    }
}

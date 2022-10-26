<?php

namespace Tests\Feature\App\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use DB;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    CONST URL = "/api/auth";

    public function testLoginSuccesfuly()
    {
        $user = User::factory()->raw();
        Self::register($user);

        $response = Self::login($user);
        $responseJson = $response->json();

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token', $responseJson);
    }

    public function testLoginWithWrongParameters()
    {
        $user = User::factory()->raw();
        Self::register($user);

        $response = Self::login($user, true);

        $response->assertStatus(422);
    }

    public function testLogoutSuccessfuly()
    {
        $user = User::factory()->raw();
        Self::register($user);
        $token = Self::login($user)->json('token');

        $response = Self::logout($token);

        $responseJson = $response->json();

        $this->assertEquals($responseJson['message'], 'User successfully signed out');
    }

    public function testRefreshSuccessfuly()
    {
        $user = User::factory()->raw();
        Self::register($user);
        $token = Self::login($user)->json('token');

        $response = Self::refresh($token);

        $responseJson = $response->json();

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token', $responseJson);
    }

    private function register($userFactory)
    {
        $this->artisan('register:user --password=' . $userFactory['password'])
            ->expectsQuestion('Please enter the first name', $userFactory['first_name'])
            ->expectsQuestion('Please enter the surname', $userFactory['surname'])
            ->expectsQuestion('Please enter a e-mail', $userFactory['email'])
            ->expectsQuestion('Are you sure?', true);
    }

    private function login($user, $withoutPassword = false)
    {
        $urlLogin = Self::URL . "/login";

        $credentials = [
            "email"    => $user["email"],
            "password" => $withoutPassword == false ? $user["password"] : null,
        ];

        $response = $this->post($urlLogin, $credentials);

        return $response;
    }

    private function logout($token)
    {
        $urlLogout = Self::URL . "/logout";

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->post($urlLogout);

        return $response;
    }

    private function refresh($token)
    {
        $urlRefresh = Self::URL . "/refresh";

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->post($urlRefresh);

        return $response;
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\ApiTestCase;
use Tests\MailTrap;
use Vanguard\Services\Auth\Api\Token;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class AuthControllerTest extends ApiTestCase
{
    use MailTrap;

    public function test_login()
    {
        $credentials = [
            'username' => 'foo',
            'password' => 'bar'
        ];

        $user = factory(User::class)->create($credentials);

        $this->postJson("/api/login", $credentials);
        $this->assertResponseOk();

        $token = Token::where('user_id', $user->id)->first();

        $this->assertJwtTokenContains($token->id);
    }

    public function test_last_login_timestamp_is_updated_after_login()
    {
        $credentials = [
            'username' => 'foo',
            'password' => 'bar'
        ];

        $now = Carbon::now();

        Carbon::setTestNow($now);

        $user = factory(User::class)->create($credentials);

        $this->seeInDatabase('users', [
            'id' => $user->id,
            'last_login' => null
        ]);

        $this->postJson("/api/login", $credentials);

        $this->assertResponseOk()
            ->seeInDatabase('users', [
                'id' => $user->id,
                'last_login' => $now
            ]);
    }

    public function test_login_with_invalid_credentials()
    {
        $credentials = [
            'username' => 'foo',
            'password' => 'bar'
        ];

        factory(User::class)->create($credentials);

        $this->postJson("/api/login", [
            'username' => 'foo',
            'password' => 'invalid'
        ]);

        $this->assertResponseStatus(401);
        $this->seeJsonContains([
            'error' => "Invalid credentials."
        ]);
    }

    public function test_login_when_credentials_are_not_provided()
    {
        $this->postJson("/api/login");

        $this->assertResponseStatus(422);
        $this->seeJsonContains([
            'username' => [
                trans('validation.required', ['attribute' => 'username'])
            ],
            'password' => [
                trans('validation.required', ['attribute' => 'password'])
            ]
        ]);
    }

    public function test_banned_user_cannot_log_in()
    {
        $credentials = [
            'username' => 'foo',
            'password' => 'bar'
        ];

        $user = factory(User::class)->create(array_merge($credentials, [
            'status' => UserStatus::BANNED
        ]));

        $this->postJson("/api/login", $credentials);

        $this->assertResponseStatus(401);
        $this->seeJsonContains([
            'error' => "Your account is banned by administrators."
        ]);
        $this->dontSeeInDatabase('api_tokens', ['user_id' => $user->id]);
    }

    public function test_unconfirmed_user_cannot_log_in()
    {
        $credentials = [
            'username' => 'foo',
            'password' => 'bar'
        ];

        $user = factory(User::class)->create(array_merge($credentials, [
            'status' => UserStatus::UNCONFIRMED
        ]));

        $this->postJson("/api/login", $credentials);

        $this->assertResponseStatus(401);
        $this->seeJsonContains([
            'error' => "Please confirm your email address first."
        ]);
        $this->dontSeeInDatabase('api_tokens', ['user_id' => $user->id]);
    }

    public function test_logout()
    {
        $credentials = [
            'username' => 'foo',
            'password' => 'bar'
        ];

        Carbon::setTestNow(Carbon::now());

        $user = factory(User::class)->create($credentials);

        $this->postJson("/api/login", $credentials);

        $token = Token::where('user_id', $user->id)->first();

        $response = $this->decodeResponseJson();

        $this->postJson("/api/logout", [], [
            'Authorization' => "Bearer {$response['token']}"
        ]);

        $this->dontSeeInDatabase('api_tokens', ['id' => $token->id])
            ->assertNull(auth('api')->user());
    }

    public function test_logout_if_token_is_not_provided()
    {
        $this->postJson("/api/logout");

        $this->assertResponseStatus(401);
    }
}

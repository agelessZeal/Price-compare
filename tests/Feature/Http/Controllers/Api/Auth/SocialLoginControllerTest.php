<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use Laravel\Socialite\Two\FacebookProvider;
use Tests\Feature\ApiTestCase;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Services\Auth\Api\Token;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;
use Mockery as m;
use Laravel\Socialite\Contracts\User as SocialUserContract;

class SocialLoginControllerTest extends ApiTestCase
{
    public function test_social_authentication_for_first_time()
    {
        $this->setSettings([
            'reg_enabled' => true
        ]);

        $socialUser = new StubSocialUser;

        $this->mockFacebookProvider($socialUser);

        $now = Carbon::now()->addHours(2);
        Carbon::setTestNow($now);

        $this->postJson("/api/login/social", [
            'network' => 'facebook',
            'social_token' => 'foo'
        ]);

        $this->assertResponseOk();

        $user = User::whereEmail($socialUser->getEmail())->first();

        $token = Token::where('user_id', $user->id)->first();

        $this->seeInDatabase('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $socialUser->getEmail(),
            'status' => UserStatus::ACTIVE,
            'avatar' => $socialUser->getAvatar(),
            'last_login' => $now
        ]);

        $this->seeInDatabase('social_logins', [
            'user_id' => $user->id,
            'provider' => 'facebook',
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar()
        ]);

        $this->assertJwtTokenContains($token->id);
    }

    public function test_associate_social_account_with_existing_user()
    {
        $this->setSettings([
            'reg_enabled' => true
        ]);

        $socialUser = new StubSocialUser;

        $this->mockFacebookProvider($socialUser);

        $user = factory(User::class)->create([
            'email' => $socialUser->getEmail()
        ]);

        $this->postJson("/api/login/social", [
            'network' => 'facebook',
            'social_token' => 'foo'
        ]);

        $this->assertResponseOk();

        $this->seeInDatabase('social_logins', [
            'user_id' => $user->id,
            'provider' => 'facebook',
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar()
        ]);

        $token = Token::where('user_id', $user->id)->first();

        $this->assertJwtTokenContains($token->id);
    }

    public function test_social_login_if_registration_is_disabled()
    {
        $this->setSettings([
            'reg_enabled' => false
        ]);

        $socialUser = new StubSocialUser;

        $this->mockFacebookProvider($socialUser);

        $this->postJson("/api/login/social", [
            'network' => 'facebook',
            'social_token' => 'foo'
        ]);

        $this->assertResponseStatus(403)
            ->seeJsonContains([
                'error' => "Only users who already created an account can log in."
            ]);
    }

    public function test_social_login_with_invalid_provider()
    {
        $this->postJson("/api/login/social", [
            'network' => 'foo',
            'social_token' => 'bar'
        ]);

        $this->assertResponseStatus(422)
            ->seeJsonContains([
                'network' => [trans('validation.in', ['attribute' => 'network'])]
            ]);
    }

    public function test_social_login_for_banned_user()
    {
        $socialUser = new StubSocialUser;

        $this->mockFacebookProvider($socialUser);

        $user = factory(User::class)->create([
            'email' => $socialUser->getEmail(),
            'status' => UserStatus::BANNED
        ]);

        app(UserRepository::class)->associateSocialAccountForUser($user->id, 'facebook', $socialUser);

        $this->postJson("/api/login/social", [
            'network' => 'facebook',
            'social_token' => 'foo'
        ]);

        $this->assertResponseStatus(403)
            ->seeJsonContains([
                'error' => 'Your account is banned by administrators.'
            ]);
    }

    private function mockFacebookProvider($socialUser)
    {
        $provider = m::mock(FacebookProvider::class);
        $provider->shouldReceive('userFromToken')->with('foo')->andReturn($socialUser);

        \Socialite::shouldReceive('driver')->with('facebook')->andReturn($provider);
    }
}

class StubSocialUser implements SocialUserContract
{
    public function getId()
    {
        return '123';
    }

    public function getNickname()
    {
        return 'johndoe';
    }

    public function getName()
    {
        return 'John Doe';
    }

    public function getEmail()
    {
        return 'john@doe.com';
    }

    public function getAvatar()
    {
        return 'http://www.gravatar.com/avatar';
    }
}

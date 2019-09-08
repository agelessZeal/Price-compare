<?php

namespace Tests\Feature\Http\Controllers\Web\Auth;

use Auth;
use DB;
use Settings;
use Socialite;
use Tests\Feature\FunctionalTestCase;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;
use Laravel\Socialite\Contracts\User as SocialUserContract;
use Mockery as m;

class SocialAuthControllerTest extends FunctionalTestCase
{
    public function test_social_login_for_new_user()
    {
        $this->setSettings(['reg_enabled' => true]);

        $socialUser = new StubSocialUser;

        $driver = m::mock(\Laravel\Socialite\Contracts\Provider::class);
        $driver->shouldReceive('user')->andReturn($socialUser);

        Socialite::shouldReceive('driver')->with('foo')->andReturn($driver);

        $this->visit("auth/foo/callback");

        $this->assertUserCreatedAndLoggedIn($socialUser, 'foo');
    }

    public function test_social_login_for_new_user_if_registration_is_disabled()
    {
        Settings::set('reg_enabled', false);

        $socialUser = new StubSocialUser;

        $driver = m::mock(\Laravel\Socialite\Contracts\Provider::class);
        $driver->shouldReceive('user')->andReturn($socialUser);

        Socialite::shouldReceive('driver')->with('foo')->andReturn($driver);

        $this->visit("auth/foo/callback");

        $this->seePageIs('login')
            ->see('Only users who already created an account can log in.');
    }

    public function test_social_login_for_banned_user()
    {
        $user = factory(User::class)->create(['status' => UserStatus::BANNED]);
        $socialUser = new StubSocialUser;

        $driver = m::mock(\Laravel\Socialite\Contracts\Provider::class);
        $driver->shouldReceive('user')->andReturn($socialUser);

        Socialite::shouldReceive('driver')->with('foo')->andReturn($driver);

        DB::table('social_logins')->insert([
            'user_id' => $user->id,
            'provider' => 'foo',
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
            'created_at' => \Carbon\Carbon::now()
        ]);

        $this->visit("auth/foo/callback");

        $this->seePageIs('/login')
            ->see(trans('app.your_account_is_banned'));
    }

    public function test_social_login_for_existing_user()
    {
        $user = factory(User::class)->create();
        $socialUser = new StubSocialUser;

        $driver = m::mock(\Laravel\Socialite\Contracts\Provider::class);
        $driver->shouldReceive('user')->andReturn($socialUser);

        Socialite::shouldReceive('driver')->with('foo')->andReturn($driver);

        DB::table('social_logins')->insert([
            'user_id' => $user->id,
            'provider' => 'foo',
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
            'created_at' => \Carbon\Carbon::now()
        ]);

        $this->visit("auth/foo/callback");

        $this->seePageIs('/');
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_twitter_login()
    {
        $this->setSettings(['reg_enabled' => true]);

        $socialUser = new StubSocialUserWithoutEmail;
        $driver = m::mock(\Laravel\Socialite\Contracts\Provider::class);
        $driver->shouldReceive('user')->andReturn($socialUser);
        Socialite::shouldReceive('driver')->with('twitter')->andReturn($driver);

        $this->visit("auth/twitter/callback")
            ->seePageIs('auth/twitter/email')
            ->seeInSession('social.user', $socialUser);

        $socialUser->email = 'john@doe.com';

        $this->type('john@doe.com', 'email')
            ->press('Log Me In')
            ->assertUserCreatedAndLoggedIn($socialUser, 'twitter');
    }

    public function test_missing_email_for_non_twitter_provider()
    {
        $this->setSettings(['reg_enabled' => true]);

        $socialUser = new StubSocialUserWithoutEmail;
        $driver = m::mock(\Laravel\Socialite\Contracts\Provider::class);
        $driver->shouldReceive('user')->andReturn($socialUser);
        Socialite::shouldReceive('driver')->with('foo')->andReturn($driver);

        $this->visit("auth/foo/callback")
            ->seePageIs('login')
            ->see("You have to provide your email address.");
    }

    public function test_social_login_for_user_with_one_word_name()
    {
        $this->setSettings(['reg_enabled' => true]);

        $socialUser = new StubSocialUserWithOneWordName;

        $driver = m::mock(\Laravel\Socialite\Contracts\Provider::class);
        $driver->shouldReceive('user')->andReturn($socialUser);

        Socialite::shouldReceive('driver')->with('foo')->andReturn($driver);

        $this->visit("auth/foo/callback");

        $this->seeInDatabase('users', [
            'username'   => null,
            'email'      => $socialUser->getEmail(),
            'first_name' => 'John',
            'last_name'  => '',
            'status'     => UserStatus::ACTIVE
        ]);

        $user = User::where('email', $socialUser->getEmail())->first();

        $this->seeInDatabase('social_logins', [
            'user_id'     => $user->id,
            'provider'    => 'foo',
            'provider_id' => $socialUser->getId(),
            'avatar'      => $socialUser->getAvatar()
        ]);

        $this->seePageIs('/');
        $this->assertEquals($user->id, Auth::id());
    }

    /**
     * @param $socialUser
     */
    private function assertUserCreatedAndLoggedIn($socialUser, $provider)
    {
        $this->seeInDatabase('users', [
            'username'   => null,
            'email'      => $socialUser->getEmail(),
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'status'     => UserStatus::ACTIVE
        ]);

        $user = User::where('email', $socialUser->getEmail())->first();

        $this->seeInDatabase('social_logins', [
            'user_id'     => $user->id,
            'provider'    => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar'      => $socialUser->getAvatar()
        ]);

        $this->seePageIs('/');
        $this->assertEquals($user->id, Auth::id());
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

class StubSocialUserWithoutEmail extends StubSocialUser
{
    public $email = null;

    public function getEmail()
    {
        return $this->email;
    }
}

class StubSocialUserWithOneWordName extends StubSocialUser
{
    public function getName()
    {
        return 'John';
    }
}
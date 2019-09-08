<?php

namespace Tests\Feature\Listeners;

use Mockery as m;
use Vanguard\Services\Logging\UserActivity\Activity;
use Vanguard\User;

class UserEventsSubscriberTest extends BaseListenerTestCase
{
    protected $theUser;

    public function setUp()
    {
        parent::setUp();
        $this->theUser = factory(\Vanguard\User::class)->create();
    }

    public function test_onLogin()
    {
        event(new \Vanguard\Events\User\LoggedIn);
        $this->assertMessageLogged('Logged in.');
    }

    public function test_onLogout()
    {
        event(new \Vanguard\Events\User\LoggedOut());
        $this->assertMessageLogged('Logged out.');
    }

    public function test_onRegister()
    {
        $this->setSettings([
            'reg_enabled' => true,
            'reg_email_confirmation' => true,
        ]);

        $user = factory(\Vanguard\User::class)->create();

        event(new \Vanguard\Events\User\Registered($user));

        $this->assertMessageLogged('Created an account.', $user);
    }

    public function test_onAvatarChange()
    {
        event(new \Vanguard\Events\User\ChangedAvatar);
        $this->assertMessageLogged('Updated profile avatar.');
    }

    public function test_onProfileDetailsUpdate()
    {
        event(new \Vanguard\Events\User\UpdatedProfileDetails);
        $this->assertMessageLogged('Updated profile details.');
    }

    public function test_onDelete()
    {
        event(new \Vanguard\Events\User\Deleted($this->theUser));

        $message = sprintf(
            "Deleted user %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onBan()
    {
        event(new \Vanguard\Events\User\Banned($this->theUser));

        $message = sprintf(
            "Banned user %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onUpdateByAdmin()
    {
        event(new \Vanguard\Events\User\UpdatedByAdmin($this->theUser));

        $message = sprintf(
            "Updated profile details for %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onCreate()
    {
        event(new \Vanguard\Events\User\Created($this->theUser));

        $message = sprintf(
            "Created an account for user %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onSettingsUpdate()
    {
        event(new \Vanguard\Events\Settings\Updated);
        $this->assertMessageLogged('Updated website settings.');
    }

    public function test_onTwoFactorEnable()
    {
        event(new \Vanguard\Events\User\TwoFactorEnabled);
        $this->assertMessageLogged('Enabled Two-Factor Authentication.');
    }

    public function test_onTwoFactorDisable()
    {
        event(new \Vanguard\Events\User\TwoFactorDisabled);
        $this->assertMessageLogged('Disabled Two-Factor Authentication.');
    }

    public function test_onTwoFactorEnabledByAdmin()
    {
        event(new \Vanguard\Events\User\TwoFactorEnabledByAdmin($this->theUser));

        $message = sprintf(
            "Enabled Two-Factor Authentication for user %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onTwoFactorDisabledByAdmin()
    {
        event(new \Vanguard\Events\User\TwoFactorDisabledByAdmin($this->theUser));

        $message = sprintf(
            "Disabled Two-Factor Authentication for user %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onPasswordResetEmailRequest()
    {
        event(new \Vanguard\Events\User\RequestedPasswordResetEmail($this->user));
        $this->assertMessageLogged("Requested password reset email.");
    }

    public function test_onPasswordReset()
    {
        event(new \Vanguard\Events\User\ResetedPasswordViaEmail($this->user));
        $this->assertMessageLogged("Reseted password using \"Forgot Password\" option.");
    }
}

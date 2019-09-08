<?php

namespace Tests\Feature\Http\Controllers\Api\Profile;

use Authy;
use Settings;
use Tests\Feature\ApiTestCase;
use Vanguard\Transformers\UserTransformer;
use Vanguard\User;

class TwoFactorControllerTest extends ApiTestCase
{
    public function test_update_2fa_unathenticated()
    {
        $user = factory(User::class)->create();

        $this->putJson("api/me/2fa")
            ->assertResponseStatus(401);
    }

    public function test_enable_two_factor_auth()
    {
        $user = $this->login();

        Settings::set('2fa.enabled', true);

        Authy::shouldReceive('isEnabled')->andReturn(false);
        Authy::shouldReceive('register')->andReturnNull();

        $data = ['country_code' => '1', 'phone_number' => '123'];
 
        $this->putJson("api/me/2fa", $data);

        $transformer = new UserTransformer;
        $updatedUser = $transformer->transform($user->fresh());

        $this->assertResponseOk()
            ->seeInDatabase('users', [
                'id' => $user->id,
                'two_factor_country_code' => $data['country_code'],
                'two_factor_phone' => $data['phone_number']
            ])
            ->seeJsonContains($updatedUser);
    }

    public function test_enable_two_factor_auth_when_it_is_already_enabled()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'users.manage');

        Settings::set('2fa.enabled', true);

        Authy::shouldReceive('isEnabled')->andReturn(true);

        $data = ['country_code' => '1', 'phone_number' => '123'];

        $this->putJson("api/me/2fa", $data);

        $this->assertResponseStatus(422)
            ->seeJsonContains([
                'error' => '2FA is already enabled for this user.'
            ]);
    }

    public function test_disable_two_factor_auth()
    {
        $user = factory(User::class)->create([
            'two_factor_country_code' => '1',
            'two_factor_phone' => '123'
        ]);

        $this->addPermissionForUser($user, 'users.manage');

        $this->be($user, 'api');

        Settings::set('2fa.enabled', true);

        Authy::shouldReceive('isEnabled')->andReturn(true);
        Authy::shouldReceive('delete')->andReturnNull();

        $this->deleteJson("api/me/2fa");

        $transformer = new UserTransformer;
        $user = $transformer->transform($user->fresh());

        $this->assertResponseOk()
            ->seeJsonContains($user);
    }

    public function test_disable_2fa_when_it_is_already_disabled()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'users.manage');

        Settings::set('2fa.enabled', true);

        Authy::shouldReceive('isEnabled')->andReturn(false);

        $this->deleteJson("api/me/2fa");

        $this->assertResponseStatus(422)
            ->seeJsonContains([
                'error' => '2FA is not enabled for this user.'
            ]);
    }
}

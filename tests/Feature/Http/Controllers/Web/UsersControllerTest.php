<?php

namespace Tests\Feature\Http\Controllers\Web;

use Authy;
use Hash;
use Input;
use Settings;
use Tests\Feature\FunctionalTestCase;
use Vanguard\Events\User\Banned;
use Vanguard\Events\User\TwoFactorEnabledByAdmin;
use Vanguard\Events\User\UpdatedByAdmin;
use Vanguard\Role;
use Vanguard\Services\Auth\Api\Token;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;
use Carbon\Carbon;
use Mockery as m;

class UsersControllerTest extends FunctionalTestCase
{
    protected function validParams(array $overrides = [])
    {
        return array_merge([
            'role_id' => Role::whereName('User')->first()->id,
            'status' => UserStatus::ACTIVE,
            'first_name' => 'foo',
            'last_name' => 'bar',
            'birthday' => Carbon::now()->subYears(25)->format('Y-m-d'),
            'phone' => '12345667',
            'address' => 'the address',
            'country_id' => 688 //Serbia,
        ], $overrides);
    }

    public function test_users_table_is_displayed()
    {
        $this->be($this->makeSuperUser());

        $active = factory(User::class)->times(4)->create();
        $banned = factory(User::class)->times(2)->create(['status' => UserStatus::BANNED]);
        $unconfirmed = factory(User::class)->times(2)->create(['status' => UserStatus::UNCONFIRMED]);

        $users = $active->merge($banned);
        $users = $users->merge($unconfirmed);

        $users = $users->sortByDesc('id')->values();

        $this->visit('user')
            ->seeInElement("h1.page-header", "Users")
            ->seeInElement('h1.page-header small', 'list of registered users')
            ->seeElement("#add-user");

        foreach ($users as $i => $user) {
            $this->seeInTable("#users-table-wrapper table", $user->present()->username, $i + 1, 1);
            $this->seeInTable("#users-table-wrapper table", $user->present()->name, $i + 1, 2);
            $this->seeInTable("#users-table-wrapper table", $user->email, $i + 1, 3);
            $this->seeInTable("#users-table-wrapper table", $user->created_at->format('Y-m-d'), $i + 1, 4);
            $this->seeInTable("#users-table-wrapper table", $user->status, $i + 1, 5);
        }
    }

    public function test_users_pagination()
    {
        $this->be($this->makeSuperUser());

        factory(User::class)->times(21)->create();

        $this->visit('user');

        $pagination = $this->crawler->filter(".pagination");
        $paginationLinks = $this->crawler->filter('.pagination a');

        $this->assertEquals(1, $pagination->count());
        $this->assertEquals(2, $paginationLinks->count());
    }

    public function test_users_search()
    {
        $user1 = factory(User::class)->create(['first_name' => 'Milos', 'last_name' => 'Stojanovic']);
        $user2 = factory(User::class)->create(['first_name' => 'John', 'last_name' => 'Doe']);
        $user3 = factory(User::class)->create(['first_name' => 'Jane', 'last_name' => 'Doe']);

        $this->be($this->makeSuperUser($user1));

        $this->visit('user')
            ->submitForm('search-users-btn', ['search' => 'doe'])
            ->seePageIs('user?search=doe&status=')
            ->seeInField('search', 'doe')
            ->seeInTable('table', $user3->present()->name, 1, 2)
            ->seeInTable('table', $user2->present()->name, 2, 2);
    }

    public function test_users_filter_by_status()
    {
        $this->be($this->makeSuperUser());

        factory(User::class)->times(2)->create();
        factory(User::class)->times(3)->create();

        $this->visit('user?status=' . UserStatus::BANNED)
            ->seeIsSelected('status', UserStatus::BANNED);

        $this->assertEquals(1, $this->crawler->filter('#users-table-wrapper table tbody tr')->count());
    }

    public function test_add_user()
    {
        $this->beSuperUser();

        $this->visit('user')
            ->click('Add User')
            ->seePageIs('user/create');

        $data = $this->stubUserData();

        $this->submitForm('Create User', $data);

        $user = User::where('email', $data['email'])->first();

        $expected = array_except($data, ['password', 'password_confirmation']);

        $this->seeInDatabase('users', $expected)
            ->seePageIs('user')
            ->see('User created successfully.')
            ->assertTrue(Hash::check('123123', $user->password));
    }

    public function test_add_user_without_country()
    {
        $this->beSuperUser();

        $this->visit('user')
            ->click('Add User')
            ->seePageIs('user/create');

        $data = $this->stubUserData();
        $data['country_id'] = 0;

        $this->submitForm('Create User', $data);

        $user = User::where('email', $data['email'])->first();

        $expected = array_except($data, ['password', 'password_confirmation']);
        $expected['country_id'] = null;

        $this->seeInDatabase('users', $expected)
            ->seePageIs('user')
            ->see('User created successfully.')
            ->assertTrue(Hash::check('123123', $user->password));
    }

    public function test_add_user_validation()
    {
        $this->beSuperUser();

        $this->visit('user/create')
            ->press('Create User')
            ->seePageIs('user/create')
            ->see('The email field is required.')
            ->see('The password field is required.');

        $this->visit('user/create')
            ->type('asdfa', 'email')
            ->type('123', 'password')
            ->type('122', 'password_confirmation')
            ->press('Create User')
            ->seePageIs('user/create')
            ->see('The email must be a valid email address.')
            ->see('The password must be at least 6 characters.')
            ->see('The password confirmation does not match.');
    }

    public function test_view_user_page()
    {
        $user = $this->createAndLoginAdminUser();

        $this->visit('user')
            ->clickOn('a[title="View User"]')
            ->seePageIs("user/{$user->id}/show")
            ->seeLink('Edit', route('user.edit', $user->id));
    }

    public function test_update_user_details()
    {
        $this->expectsEvents(UpdatedByAdmin::class);

        $this->createAndLoginAdminUser();

        $user = $this->createUser();
        $user = $this->setRoleForUser($user, 'User');

        $this->visit('user')
            ->seeLink('', "user/{$user->id}/edit");

        $this->visit("user/{$user->id}/edit");

        $data = $this->validParams();

        $this->submitForm('update-details-btn', $data);

        $expected = $data + ['id' => $user->id];

        $this->seeInDatabase('users', $expected)
            ->seePageIs("user/{$user->id}/edit")
            ->see('User updated successfully.');
    }

    /** @test */
    public function banning_user_will_invalidate_all_his_sessions_and_api_tokens()
    {
        putenv('SESSION_DRIVER=database');

        $this->refreshApp();

        $this->createAndLoginAdminUser();

        $user = $this->createUser([
            'remember_token' => str_random(60)
        ]);

        $user = $this->setRoleForUser($user, 'User');

        \DB::table('sessions')->insert([
            'id' => str_random(40),
            'user_id' => $user->id,
            'ip_address' => "127.0.0.1",
            'user_agent' => 'Foo',
            'payload' => str_random(),
            'last_activity' => Carbon::now()->subMinute()->timestamp
        ]);

        factory(Token::class)->create(['user_id' => $user->id]);

        $this->visit('user')
            ->seeLink('', "user/{$user->id}/edit");

        $this->visit("user/{$user->id}/edit");

        $data = $this->validParams([
            'status' => UserStatus::BANNED
        ]);

        $this->submitForm('update-details-btn', $data);

        $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);
        $this->assertNull($user->fresh()->remember_token);
        $this->assertDatabaseMissing('api_tokens', ['user_id' => $user->id]);
    }

    public function test_update_user_login_details()
    {
        $user = $this->createAndLoginAdminUser([
            'email' => 'test@test.com',
            'username' => 'test',
            'password' => 'milos123'
        ]);

        $data = [
            'email' => 'john@doe.com',
            'username' => 'milos',
            'password' => '123123',
            'password_confirmation' => '123123'
        ];

        $this->visit("user/{$user->id}/edit")
            ->submitForm("update-login-details-btn", $data)
            ->seePageIs("user/{$user->id}/edit")
            ->see('Login details updated successfully.');

        $user = $user->fresh();

        $this->assertEquals($data['email'], $user->email);
        $this->assertEquals($data['username'], $user->username);
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }

    public function test_2fa_form_visibility()
    {
        Settings::set('2fa.enabled', false);
        $user = $this->createAndLoginAdminUser();

        $this->visit("user/{$user->id}/edit")
            ->dontSee('Two-Factor Authentication');

        Settings::set('2fa.enabled', true);

        $this->visit("user/{$user->id}/edit")
            ->see('Two-Factor Authentication');
    }

    public function test_enable_2fa_for_user()
    {
        $this->expectsEvents(TwoFactorEnabledByAdmin::class);

        Settings::set('2fa.enabled', true);
        $user = $this->createAndLoginAdminUser();

        Authy::shouldReceive('isEnabled')->andReturn(false);
        Authy::shouldReceive('register')->andReturnNull();

        $data = ['country_code' => '1', 'phone_number' => '123'];

        $this->visit("user/{$user->id}/edit")
            ->submitForm('Enable', $data)
            ->seePageIs("user/{$user->id}/edit")
            ->seeInDatabase('users', [
                'id' => $user->id,
                'two_factor_country_code' => $data['country_code'],
                'two_factor_phone' => $data['phone_number']
            ])
            ->see('Two-Factor Authentication enabled successfully.');
    }

    public function test_disable_2fa_for_user()
    {
        $this->expectsEvents(\Vanguard\Events\User\TwoFactorDisabledByAdmin::class);

        Settings::set('2fa.enabled', true);
        $user = $this->createAndLoginAdminUser();

        Authy::shouldReceive('isEnabled')->andReturn(true);
        Authy::shouldReceive('delete')->andReturnNull();

        $this->visit("user/{$user->id}/edit")
            ->press('Disable')
            ->seePageIs("user/{$user->id}/edit")
            ->seeInDatabase('users', [
                'id' => $user->id,
                'two_factor_country_code' => null,
                'two_factor_phone' => null
            ])
            ->see('Two-Factor Authentication disabled successfully.');
    }

    public function test_avatar_update()
    {
        $user = $this->createAndLoginAdminUser();

        $uploads = ['avatar' => base_path('tests/files/image.png')];

        $input = [
            'points' => [
                'x1' => 0,
                'y1' => 0,
                'x2' => 200,
                'y2' => 200
            ]
        ] + $uploads;

        $this->visit("user/{$user->id}/edit")
            ->submitForm('Save', $input, $uploads);

        $this->seePageIs("user/{$user->id}/edit")
            ->see('Avatar changed successfully.');

        $user = $user->fresh();

        $uploadedFile = public_path("upload/users/{$user->avatar}");

        $this->assertNotNull($user->avatar);
        $this->assertFileExists($uploadedFile);

        list($width, $height) = getimagesize($uploadedFile);

        $this->assertEquals(160, $width);
        $this->assertEquals(160, $height);

        @unlink($uploadedFile);
    }

    public function test_avatar_update_with_invalid_file()
    {
        $user = $this->createAndLoginAdminUser();

        $file = storage_path("app/vanguard-test.php");

        file_put_contents($file, "<?php ");

        $uploads = ['avatar' => $file];

        $input = [
            'points' => [
                'x1' => 0,
                'y1' => 0,
                'x2' => 200,
                'y2' => 200
            ]
        ] + $uploads;

        $this->visit("user/{$user->id}/edit")
            ->submitForm('Save', $input, $uploads);

        $this->seePageIs("user/{$user->id}/edit")
            ->see("The avatar must be an image.");

        $user = $user->fresh();

        $this->assertNull($user->avatar);

        @unlink($file);
    }

    /**
     * @expectedException \Laravel\BrowserKitTesting\HttpException
     */
    public function test_session_page_is_not_available_for_non_database_driver()
    {
        putenv('SESSION_DRIVER=array');

        $this->refreshApp();

        $user = $this->createAndLoginAdminUser();

        $this->visit('user')
            ->dontSeeElement('a[title="User Sessions"]');

        // this page should not be accessible if
        // database session driver is not being used
        $this->visit("user/{$user->id}/sessions");
    }

    public function test_invalidate_session()
    {
        putenv('SESSION_DRIVER=database');

        $this->refreshApp();

        Carbon::setTestNow(Carbon::now());

        $user = $this->createAndLoginAdminUser();

        $agent = $this->app['agent'];
        $device = $agent->device() ?: 'Unknown';
        $platform = $agent->platform() ?: 'Unknown';

        $this->visit('user')
            ->clickOn('a[title="User Sessions"]')
            ->seePageIs("user/{$user->id}/sessions")
            ->seeInTable('table', Input::ip(), 1, 1)
            ->seeInTable('table', "{$device} ({$platform})", 1, 2)
            ->seeInTable('table', $agent->browser(), 1, 3)
            ->seeInTable('table', Carbon::now()->format(config('app.date_time_format')), 1, 4);

        $this->assertEquals(1, $this->crawler->filter("table tbody tr")->count());

        $url = $this->crawler->filter('a[title="Invalidate Session"]')->first()->link()->getUri();

        $this->delete($url)
            ->followRedirects()
            ->dontSeeInDatabase('sessions', ['user_id' => $user->id])
            ->seePageIs("user/{$user->id}/sessions")
            ->see('Session invalidated successfully.');
    }

    public function test_delete_user()
    {
        $this->createAndLoginAdminUser();

        $user = $this->createUser();
        $user = $this->setRoleForUser($user, 'User');

        $this->delete(route('user.delete', $user->id))
            ->followRedirects()
            ->seePageIs('user')
            ->see("User deleted successfully.");
    }

    public function test_if_user_can_delete_himself()
    {
        $admin = $this->createAndLoginAdminUser();

        $this->delete(route('user.delete', $admin->id))
            ->followRedirects()
            ->seePageIs('user')
            ->see("You cannot delete yourself.");
    }

    /**
     * @param array $override
     * @return array
     */
    private function stubUserData(array $override = [])
    {
        return array_merge([
            'role_id' => Role::where('name', 'User')->first()->id,
            'status' => UserStatus::ACTIVE,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birthday' => Carbon::now()->subYears(20)->format('Y-m-d'),
            'phone' => '123456',
            'address' => 'some address',
            'country_id' => 688,
            'email' => 'john@doe.com',
            'username' => 'johndoe',
            'password' => '123123',
            'password_confirmation' => '123123'
        ], $override);
    }

    private function beSuperUser()
    {
        $user = $this->createSuperUser();

        $this->be($user);

        return $user;
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Api\Users;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\Feature\ApiTestCase;
use Tests\Feature\FunctionalTestCase;
use Vanguard\Country;
use Vanguard\Events\User\Deleted;
use Vanguard\Events\User\UpdatedByAdmin;
use Vanguard\Role;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\Transformers\SessionTransformer;
use Vanguard\Transformers\UserTransformer;
use Vanguard\User;

class UsersControllerTest extends ApiTestCase
{
    public function test_unauthenticated()
    {
        $this->getJson('/api/users')
            ->assertResponseStatus(401);
    }

    public function test_get_users_without_permission()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api')
            ->getJson('/api/users')
            ->assertResponseStatus(403);
    }

    public function test_paginate_all_users()
    {
        $user = $this->login();

        $users = factory(User::class)->times(20)->create();
        $users->push($user);

        $this->getJson('/api/users');

        $transformed = $this->transformCollection(
            $users->sortByDesc('id')->take(20),
            new UserTransformer
        );

        $response = $this->decodeResponseJson();

        $this->assertEquals($response['data'], $transformed);
        $this->assertEquals($response['meta'], [
            'current_page' => 1,
            'from' => 1,
            'to' => 20,
            'last_page' => 2,
            'prev_page_url' => null,
            'next_page_url' => url("api/users?page=2"),
            'total' => 21,
            'per_page' => 20
        ]);
    }

    public function test_paginate_users_with_status()
    {
        $user = $this->login();

        $activeUsers = factory(User::class)->times(2)->create([
            'status' => UserStatus::ACTIVE
        ]);

        $bannedUsers = factory(User::class)->times(5)->create([
            'status' => UserStatus::BANNED
        ]);

        $this->getJson('/api/users?status=' . UserStatus::BANNED);

        $response = $this->decodeResponseJson();

        $this->assertCount(5, $response['data']);
    }

    public function test_paginate_users_on_search()
    {
        $user = $this->login();

        $user1 = factory(User::class)->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@vanguardapp.io'
        ]);

        $user2 = factory(User::class)->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@vanguardapp.io'
        ]);

        $user3 = factory(User::class)->create([
            'first_name' => 'Brad',
            'last_name' => 'Pitt',
            'email' => 'b.pitt@vanguardapp.io'
        ]);

        $this->getJson('/api/users?search=doe');

        $response = $this->decodeResponseJson();

        $this->assertCount(2, $response['data']);
    }

    public function test_create_user()
    {
        $user = $this->login();

        $newUser = factory(User::class)->make();

        $data = array_merge($newUser->toArray(), [
            'birthday' => $newUser->birthday->format('Y-m-d'),
            'role' => $newUser->role_id,
            'password' => '123123',
            'password_confirmation' => '123123'
        ]);

        $this->postJson('api/users', $data);

        $expected = [
            'first_name' => $newUser->first_name,
            'last_name' => $newUser->last_name,
            'email' => $newUser->email,
            'username' => $newUser->username,
            'country_id' => $newUser->country_id,
            'birthday' => $newUser->birthday->format('Y-m-d'),
            'phone' => $newUser->phone,
            'address' => $newUser->address,
            'status' => UserStatus::ACTIVE,
            'role_id' => $newUser->role_id
        ];

        $this->assertResponseStatus(201)
            ->seeInDatabase('users', $expected)
            ->seeJsonContains($expected);
    }

    public function test_get_user()
    {
        $user = $this->login();

        $this->getJson("api/users/{$user->id}")
            ->assertResponseOk()
            ->seeJsonContains(
                (new UserTransformer)->transform($user)
            );
    }

    public function test_get_user_which_does_not_exist()
    {
        $user = $this->login();

        $this->getJson("api/users/2")
            ->assertResponseStatus(404);
    }

    public function test_update_user()
    {
        $this->expectsEvents(UpdatedByAdmin::class);

        $user = $this->login();

        $data = [
            'email' => 'john.doe@test.com',
            'username' => 'john.doe',
            'password' => '123123',
            'password_confirmation' => '123123',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+38123456789',
            'address' => 'Some random address',
            'country_id' => Country::first()->id,
            'birthday' => '1990-10-18',
            'status' => UserStatus::BANNED,
            'role_id' => Role::whereName('User')->first()->id
        ];

        $expected = array_except($data, ['password', 'password_confirmation']);
        $expected += ['id' => $user->id];

        $this->patchJson("api/users/{$user->id}", $data)
            ->assertResponseOk()
            ->seeInDatabase('users', $expected)
            ->seeJsonContains($expected);
    }

    public function test_update_only_specific_field()
    {
        $this->expectsEvents(UpdatedByAdmin::class);

        $user = $this->login();

        $data = [
            'email' => 'john.doe@test.com',
        ];

        $expected = array_merge(
            $user->toArray(),
            $data,
            ['birthday' => $user->birthday->format('Y-m-d')]
        );

        $expected = array_except($expected, ['created_at', 'updated_at', 'avatar', 'role']);

        $this->patchJson("api/users/{$user->id}", $data)
            ->assertResponseOk()
            ->seeInDatabase('users', $expected)
            ->seeJsonContains($expected);
    }

    public function test_delete_user()
    {
        $this->expectsEvents(Deleted::class);

        $user = $this->login();

        $user2 = factory(User::class)->create();

        $this->deleteJson("api/users/{$user2->id}")
            ->assertResponseOk()
            ->seeJsonContains(['success' => true]);
    }

    public function test_delete_yourself()
    {
        $user = $this->login();

        $this->deleteJson("api/users/{$user->id}")
            ->assertResponseStatus(403)
            ->seeJsonContains(['error' => "You cannot delete yourself."]);
    }

    protected function login()
    {
        $user = parent::login();

        $this->addPermissionForUser($user, 'users.manage');

        return $user;
    }
}

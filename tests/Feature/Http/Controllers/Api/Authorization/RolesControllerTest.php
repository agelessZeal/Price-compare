<?php

namespace Tests\Feature\Http\Controllers\Api\Authorization;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\Feature\ApiTestCase;
use Tests\Feature\FunctionalTestCase;
use Vanguard\Country;
use Vanguard\Role;
use Vanguard\Services\Logging\UserActivity\Activity;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\Transformers\ActivityTransformer;
use Vanguard\Transformers\RoleTransformer;
use Vanguard\Transformers\SessionTransformer;
use Vanguard\Transformers\UserTransformer;
use Vanguard\User;

class RolesControllerTest extends ApiTestCase
{
    public function test_unauthenticated()
    {
        $this->getJson('/api/roles')
            ->assertResponseStatus(401);
    }

    public function test_get_settings_without_permission()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api')
            ->getJson('/api/roles')
            ->assertResponseStatus(403);
    }

    public function test_get_roles()
    {
        $user = $this->getUser();

        factory(Role::class)->times(4)->create();

        $this->getJson("/api/roles");

        $roles = Role::withCount('users')->get();

        $this->assertResponseOk()
            ->seeJsonEquals(
                $this->transformCollection($roles, new RoleTransformer)
            );

        $response = $this->decodeResponseJson();

        $this->assertCount(6, $response);
    }

    public function test_get_role()
    {
        $user = $this->getUser();

        $userRole = Role::whereName('User')->first();

        $this->getJson("/api/roles/{$userRole->id}");

        $this->assertResponseOk()
            ->seeJsonEquals(
                (new RoleTransformer)->transform($userRole)
            );
    }

    public function test_create_role()
    {
        $this->getUser();

        $data = [
            'name' => 'foo',
            'display_name' => 'Foo Role',
            'description' => 'This is foo role.'
        ];

        $this->postJson("/api/roles", $data);

        $this->assertResponseOk()
            ->seeInDatabase('roles', $data)
            ->seeJsonContains($data);
    }

    public function test_create_role_with_invalid_name()
    {
        $this->getUser();

        $this->postJson("/api/roles");

        $this->assertResponseStatus(422)
            ->seeJsonContains([
                'name' => [
                    trans('validation.required', ['attribute' => 'name'])
                ]
            ]);

        $this->postJson("/api/roles", ['name' => 'User']);

        $this->assertResponseStatus(422)
            ->seeJsonContains([
                'name' => [
                    trans('validation.unique', ['attribute' => 'name'])
                ]
            ]);

        $this->postJson("/api/roles", ['name' => 'foo bar']);

        $this->assertResponseStatus(422)
            ->seeJsonContains([
                'name' => [
                    trans('validation.regex', ['attribute' => 'name'])
                ]
            ]);
    }

    public function test_update_role()
    {
        $user = $this->getUser();

        $data = ['name' => 'foo'];

        $this->patchJson("/api/roles/{$user->role_id}", $data);

        $expected = $data + ['id' => $user->role_id];

        $this->assertResponseOk()
            ->seeInDatabase('roles', $expected)
            ->seeJsonContains($expected);
    }

    public function test_partially_update_role()
    {
        $user = $this->getUser();

        $data = [
            'name' => 'foo',
            'display_name' => 'Foo Role',
            'description' => 'This is foo role.'
        ];

        $this->actingAs($user, 'api')->patchJson("/api/roles/{$user->role_id}", $data);

        $expected = $data + ['id' => $user->role_id];

        $this->assertResponseOk()
            ->seeInDatabase('roles', $expected)
            ->seeJsonContains($expected);
    }

    public function test_remove_role()
    {
        $userRole = Role::whereName('User')->first();

        $role = factory(Role::class)->create();

        $user = factory(User::class)->create([
            'role_id' => $role->id,
        ]);

        $this->addPermissionForUser($user, 'roles.manage');

        $this->actingAs($user, 'api')->deleteJson("/api/roles/{$role->id}");

        $this->seeStatusCode(200)
            ->seeJson(['success' => true])
            ->dontSeeInDatabase('roles', ['id' => $role->id])
            ->assertEquals($userRole->id, $user->fresh()->role_id);
    }

    public function test_remove_non_removable_role()
    {
        $this->getUser();

        $role = factory(Role::class)->create([
            'removable' => false
        ]);

        $this->deleteJson("/api/roles/{$role->id}");

        $this->seeStatusCode(403);
    }

    /**
     * @return mixed
     */
    private function getUser()
    {
        $userRole = Role::whereName('User')->first();

        $user = factory(User::class)->create([
            'role_id' => $userRole->id,
        ]);

        $this->addPermissionForUser($user, 'roles.manage');

        $this->be($user, 'api');

        return $user;
    }
}

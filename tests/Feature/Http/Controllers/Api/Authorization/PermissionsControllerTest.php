<?php

namespace Tests\Feature\Http\Controllers\Api\Authorization;

use Tests\Feature\ApiTestCase;
use Vanguard\Permission;
use Vanguard\Transformers\PermissionTransformer;
use Vanguard\User;

class PermissionsControllerTest extends ApiTestCase
{
    public function test_unauthenticated()
    {
        $this->getJson('/api/permissions')
            ->assertResponseStatus(401);
    }

    public function test_get_users_without_permission()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api')
            ->getJson('/api/permissions')
            ->assertResponseStatus(403);
    }

    public function test_get_permissions()
    {
        $this->getUser();

        factory(Permission::class)->times(3)->create();

        $this->getJson("/api/permissions");

        $permissions = Permission::all();

        $this->assertResponseOk()
            ->seeJsonEquals(
                $this->transformCollection($permissions, new PermissionTransformer)
            );

        $response = $this->decodeResponseJson();

        // 7 default permissions + 3 newly created
        $this->assertCount(10, $response);
    }

    public function test_get_permission()
    {
        $this->getUser();

        $permission = factory(Permission::class)->create();

        $this->getJson("/api/permissions/{$permission->id}");

        $this->assertResponseOk()
            ->seeJsonEquals(
                (new PermissionTransformer)->transform($permission)
            );
    }

    public function test_create_permission()
    {
        $this->getUser();

        $data = [
            'name' => 'foo',
            'display_name' => 'Foo Permission',
            'description' => 'This is foo permission.'
        ];

        $this->postJson("/api/permissions", $data);

        $this->assertResponseOk()
            ->seeInDatabase('permissions', $data)
            ->seeJsonContains($data);
    }

    public function test_create_permission_with_invalid_name()
    {
        $this->getUser();

        $this->postJson("/api/permissions");

        $this->assertResponseStatus(422)
            ->seeJsonContains([
                'name' => [
                    trans('validation.required', ['attribute' => 'name'])
                ]
            ]);

        $existingPermission = Permission::first();

        $this->postJson("/api/permissions", ['name' => $existingPermission->name]);

        $this->assertResponseStatus(422)
            ->seeJsonContains([
                'name' => [
                    trans('app.permission_already_exists')
                ]
            ]);

        $this->postJson("/api/permissions", ['name' => 'foo bar']);

        $this->assertResponseStatus(422)
            ->seeJsonContains([
                'name' => [
                    trans('validation.regex', ['attribute' => 'name'])
                ]
            ]);
    }

    public function test_partially_update_permission()
    {
        $this->getUser();

        $permission = factory(Permission::class)->create();

        $data = ['name' => 'foo'];

        $this->patchJson("/api/permissions/{$permission->id}", $data);

        $expected = $data + ['id' => $permission->id];

        $this->assertResponseOk()
            ->seeInDatabase('permissions', $expected)
            ->seeJsonContains($expected);
    }

    public function test_update_permission()
    {
        $this->getUser();

        $permission = factory(Permission::class)->create();

        $data = [
            'name' => 'foo',
            'display_name' => 'Foo Role',
            'description' => 'This is foo role.'
        ];

        $this->patchJson("/api/permissions/{$permission->id}", $data);

        $expected = $data + ['id' => $permission->id];

        $this->assertResponseOk()
            ->seeInDatabase('permissions', $expected)
            ->seeJsonContains($expected);
    }

    public function test_remove_permission()
    {
        $this->getUser();

        $permission = factory(Permission::class)->create([
            'removable' => true
        ]);

        $this->deleteJson("/api/permissions/{$permission->id}");

        $this->assertResponseOk()
            ->seeJson(['success' => true])
            ->dontSeeInDatabase('permissions', ['id' => $permission->id]);
    }

    public function test_remove_non_removable_permission()
    {
        $this->getUser();

        $permission = factory(Permission::class)->create([
            'removable' => false
        ]);

        $this->deleteJson("/api/permissions/{$permission->id}");

        $this->seeStatusCode(403);
    }

    /**
     * @return mixed
     */
    private function getUser()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'permissions.manage');

        return $user;
    }
}

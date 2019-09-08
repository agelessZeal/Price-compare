<?php

namespace Tests\Feature\Http\Controllers\Api\Authorization;

use Tests\Feature\ApiTestCase;
use Vanguard\Permission;
use Vanguard\Role;
use Vanguard\Transformers\PermissionTransformer;
use Vanguard\User;

class RolePermissionsControllerTest extends ApiTestCase
{
    public function test_unauthenticated()
    {
        $role = factory(Role::class)->create();

        $this->getJson("/api/roles/{$role->id}/permissions")
            ->assertResponseStatus(401);
    }

    public function test_get_settings_without_permission()
    {
        $role = factory(Role::class)->create();

        $user = factory(User::class)->create();

        $this->actingAs($user, 'api')
            ->getJson("/api/roles/{$role->id}/permissions")
            ->assertResponseStatus(403);
    }

    public function test_get_role_permissions()
    {
        $user = $this->getUser();

        $role = factory(Role::class)->create();
        $permission = factory(Permission::class)->create();

        $role->attachPermission($permission);

        $this->getJson("/api/roles/{$role->id}/permissions");

        $this->assertResponseOk()
            ->seeJsonEquals(
                $this->transformCollection(collect([$permission]), new PermissionTransformer)
            );
    }

    public function test_update_role_permissions()
    {
        $user = $this->getUser();

        $role = factory(Role::class)->create();
        $permissions1 = factory(Permission::class)->times(2)->create();
        $permissions2 = factory(Permission::class)->times(3)->create();

        $role->attachPermissions($permissions1);

        $this->putJson("/api/roles/{$role->id}/permissions", [
            'permissions' => $permissions2->pluck('id')
        ]);

        $this->assertResponseOk();

        foreach ($permissions2 as $perm) {
            $this->seeInDatabase('permission_role', [
                'permission_id' => $perm->id,
                'role_id' => $role->id
            ]);
        }

        $this->seeJson(
            $this->transformCollection($permissions2, new PermissionTransformer)
        );
    }

    /**
     * @return mixed
     */
    private function getUser()
    {
        $user = factory(User::class)->create();

        $this->addPermissionForUser($user, 'permissions.manage');

        $this->be($user, 'api');

        return $user;
    }
}

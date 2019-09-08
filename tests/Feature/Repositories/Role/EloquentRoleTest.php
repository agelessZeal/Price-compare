<?php

namespace Tests\Feature\Repositories\Role;

use Mockery as m;
use Tests\Feature\FunctionalTestCase;
use Vanguard\Events\Role\Created;
use Vanguard\Repositories\Role\EloquentRole;
use Vanguard\Role;
use Vanguard\User;

class EloquentRoleTest extends FunctionalTestCase
{
    /**
     * @var EloquentRole
     */
    protected $repo;

    protected $seed = false;

    public function setUp()
    {
        parent::setUp();
        $this->repo = app(EloquentRole::class);
    }

    public function test_all()
    {
        $roles = factory(Role::class)->times(4)->create();

        $this->assertEquals(
            $roles->toArray(),
            $this->repo->all()->toArray()
        );
    }

    public function test_getAllWithUsersCount()
    {
        $roles = factory(Role::class)->times(4)->create();
        $users = factory(User::class)->times(3)->create([
            'role_id' => $roles[0]->id
        ]);

        $this->setRoleForUser($users[0], $roles[0]->name);
        $this->setRoleForUser($users[1], $roles[0]->name);
        $this->setRoleForUser($users[2], $roles[1]->name);

        $roles[0]->users_count = 2;
        $roles[1]->users_count = 1;
        $roles[2]->users_count = 0;
        $roles[3]->users_count = 0;

        $this->assertEquals($roles->toArray(), $this->repo->getAllWithUsersCount()->toArray());
    }

    public function test_create()
    {
        $this->expectsEvents(Created::class);

        $data = ['name' => 'foo', 'display_name' => 'Foo'];
        $role = $this->repo->create($data);

        $this->seeInDatabase('roles', $data + ['id' => $role->id]);
    }

    public function test_update()
    {
        $this->expectsEvents(\Vanguard\Events\Role\Updated::class);

        $role = factory(Role::class)->create();

        $data = ['name' => 'foo', 'display_name' => 'Foo'];

        $this->repo->update($role->id, $data);

        $this->seeInDatabase('roles', $data + ['id' => $role->id]);
    }

    public function test_delete()
    {
        $this->expectsEvents(\Vanguard\Events\Role\Deleted::class);

        $role = factory(Role::class)->create();

        $this->repo->delete($role->id);

        $this->dontSeeInDatabase('roles', ['id' => $role->id]);
    }

    public function test_updatePermissions()
    {
        $role = factory(Role::class)->create();
        $permissions = factory(\Vanguard\Permission::class)->times(2)->create();

        $this->repo->updatePermissions($role->id, $permissions->pluck('id')->toArray());

        $this->seeInDatabase('permission_role', ['role_id' => $role->id, 'permission_id' => $permissions[0]->id]);
        $this->seeInDatabase('permission_role', ['role_id' => $role->id, 'permission_id' => $permissions[1]->id]);
    }

    public function test_lists()
    {
        $roles = factory(Role::class)->times(4)->create();
        $roles = $roles->pluck('name', 'id');

        $this->assertEquals($roles->toArray(), $this->repo->lists()->toArray());
    }
}

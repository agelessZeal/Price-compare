<?php

namespace Tests\Feature\Http\Controllers\Web;

use Tests\Feature\FunctionalTestCase;
use Vanguard\Role;
use Mockery as m;
use Vanguard\User;

class RolesControllerTest extends FunctionalTestCase
{
    protected $user;

    protected $seed = false;

    public function setUp()
    {
        parent::setUp();
        $this->user = $this->createSuperUser();
        $this->be($this->user);
    }

    public function test_roles_list_is_displayed_properly()
    {
        $role1 = factory(Role::class)->create(['removable' => true]);
        $role2 = factory(Role::class)->create(['removable' => false]);

        $this->user->setRole($role2);

        // Since there will be one more role created while super
        // user is created, we want to make sure that only those
        // two roles exist now.
        Role::whereNotIn('id', [$role1->id, $role2->id])->delete();

        $this->visit('role')
            ->seeInTable('table', $role1->name, 1, 1)
            ->seeInTable('table', $role1->display_name, 1, 2)
            ->seeInTable('table', "0", 1, 3)
            ->seeElement('table tbody tr:nth-child(1) > td a[title="Delete Role"]')
            ->seeInTable('table', $role2->name, 2, 1)
            ->seeInTable('table', $role2->display_name, 2, 2)
            ->seeInTable('table', "1", 2, 3)
            ->dontSeeElement('table tbody tr:nth-child(2) > td a[title="Delete Role"]');
    }

    public function test_create_role()
    {
        $data = $this->roleStubData();

        $this->visit('role')
            ->click('Add Role')
            ->seePageIs('role/create')
            ->submitForm('Create Role', $data);

        $this->seePageIs('role')
            ->see('Role created successfully.')
            ->seeInDatabase('roles', $data);
    }

    public function test_edit_role()
    {
        $role = factory(Role::class)->create([
            'name' => 'foo'
        ]);

        $this->visit('role')
            ->clickOn('table tbody tr:nth-child(2) > td a[title="Edit Role"]')
            ->seePageIs("role/{$role->id}/edit")
            ->seeInField('name', $role->name)
            ->seeInField('display_name', $role->display_name)
            ->seeInField('description', $role->description);

        $data = $this->roleStubData();

        $this->submitForm('Update Role', $data)
            ->seePageIs("role")
            ->see('Role updated successfully.')
            ->seeInDatabase('roles', $data + ['id' => $role->id]);
    }

    public function test_delete_role()
    {
        $this->artisan('db:seed', ['--class' => 'RolesSeeder']);

        $role = factory(Role::class)->create(['removable' => true]);

        $this->delete(route('role.delete', $role->id))
            ->dontSeeInDatabase('roles', ['id' => $role->id]);
    }

    public function test_users_receive_default_role_after_their_role_is_deleted()
    {
        $this->artisan('db:seed', ['--class' => 'RolesSeeder']);

        $user = factory(User::class)->create();
        $role = factory(Role::class)->create(['removable' => true]);
        $userRole = Role::where('name', 'User')->first();

        $this->setRoleForUser($user, $role->name);

        $this->assertTrue($user->hasRole($role->name));

        $this->delete(route('role.delete', $role->id))
            ->seeInDatabase('users', [
                'role_id' => $userRole->id,
                'id' => $user->id
            ]);

        $user = $user->fresh();

        $this->assertFalse($user->hasRole($role->name));
        $this->assertTrue($user->hasRole($userRole->name));
    }

    public function test_delete_unremovable_role()
    {
        $role = factory(Role::class)->create(['removable' => false]);

        // This call should throw an exception
        // because this role cannot be deleted
        $this->delete(route('role.delete', $role->id));

        $this->assertResponseStatus(404);
        $this->see('Oops, 404!');
    }

    /**
     * @return array
     */
    private function roleStubData()
    {
        return [
            'name' => 'foo',
            'display_name' => 'Foooooo',
            'description' => 'the description'
        ];
    }
}

<?php

namespace Tests\Feature\Repositories\Permission;

use Cache;
use Tests\Feature\FunctionalTestCase;
use Vanguard\Events\Permission\Created;
use Vanguard\Permission;
use Vanguard\Repositories\Permission\EloquentPermission;

class EloquentPermissionTest extends FunctionalTestCase
{
    /**
     * @var EloquentPermission
     */
    protected $repo;

    protected $seed = false;

    public function setUp()
    {
        parent::setUp();
        $this->repo = app(EloquentPermission::class);
    }

    public function test_all()
    {
        $permissions = factory(Permission::class)->times(4)->create();

        $this->assertEquals($permissions->toArray(), $this->repo->all()->toArray());
    }

    public function test_create()
    {
        $this->expectsEvents(Created::class);

        $data = $this->getPermissionStubData();

        $perm = $this->repo->create($data);

        $this->seeInDatabase('permissions', $data + ['id' => $perm->id]);
    }

    public function test_update()
    {
        $this->expectsEvents(\Vanguard\Events\Permission\Updated::class);

        Cache::put('foo', 'bar');

        $data = $this->getPermissionStubData();

        $perm = factory(Permission::class)->create();

        $this->repo->update($perm->id, $data);

        $this->seeInDatabase('permissions', $data + ['id' => $perm->id])
            ->assertNull(Cache::get('foo'));
    }

    public function test_delete()
    {
        $this->expectsEvents(\Vanguard\Events\Permission\Deleted::class);

        Cache::put('foo', 'bar');

        $perm = factory(Permission::class)->create();

        $this->repo->delete($perm->id);

        $this->notSeeInDatabase('permissions', ['id' => $perm->id])
            ->assertNull(Cache::get('foo'));
    }

    /**
     * @return array
     */
    private function getPermissionStubData()
    {
        return [
            'name'         => str_random(5),
            'display_name' => str_random(5),
            'description'  => 'foo',
            'removable'    => true
        ];
    }
}

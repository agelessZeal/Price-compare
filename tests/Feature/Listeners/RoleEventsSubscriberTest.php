<?php

namespace Tests\Feature\Listeners;

use Vanguard\Events\Role\Created;
use Vanguard\Events\Role\Deleted;
use Vanguard\Events\Role\PermissionsUpdated;
use Vanguard\Events\Role\Updated;

class RoleEventsSubscriberTest extends BaseListenerTestCase
{
    protected $role;

    public function setUp()
    {
        parent::setUp();
        $this->role = factory(\Vanguard\Role::class)->create();
    }

    public function test_onCreate()
    {
        event(new Created($this->role));
        $this->assertMessageLogged("Created new role called {$this->role->display_name}.");
    }

    public function test_onUpdate()
    {
        event(new Updated($this->role));
        $this->assertMessageLogged("Updated role with name {$this->role->display_name}.");
    }

    public function test_onDelete()
    {
        event(new Deleted($this->role));
        $this->assertMessageLogged("Deleted role named {$this->role->display_name}.");
    }

    public function test_onPermissionsUpdate()
    {
        event(new PermissionsUpdated($this->role));
        $this->assertMessageLogged("Updated role permissions.");
    }

}

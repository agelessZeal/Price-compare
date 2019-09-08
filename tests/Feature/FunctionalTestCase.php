<?php

namespace Tests\Feature;

use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Schema;
use Settings;
use Tests\TestCase;
use Vanguard\Permission;
use Vanguard\Role;
use Vanguard\User;
use Mockery as m;

class FunctionalTestCase extends TestCase
{
    use \Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase,
        RefreshDatabase;

    protected $seed = true;

    protected $settingsModified = [];

    public function setUp()
    {
        $this->afterApplicationCreated(function () {
            if ($this->isSQLiteConnection()) {
                DB::connection()->getPdo()->exec("pragma foreign_keys=1");
            }

            $this->artisan('migrate');

            if ($this->seed) {
                $this->artisan('db:seed', ['--class' => 'CountriesSeeder']);
                $this->artisan('db:seed', ['--class' => 'RolesSeeder']);
                $this->artisan('db:seed', ['--class' => 'PermissionsSeeder']);
            }
        });

        $this->beforeApplicationDestroyed(function () {
            if (Schema::hasTable('migrations')) {
                $this->artisan('migrate:rollback');
            }
            \DB::disconnect();
        });

        $this->beforeApplicationDestroyed(function () {
            foreach ($this->settingsModified as $key => $value) {
                Settings::set($key, $value);
            }

            Settings::save();

            $this->settingsModified = [];
        });

        parent::setUp();
    }

    /**
     * Set some setting that should automatically be
     * reverted to it's default value after each test.
     * @param array $settings
     */
    protected function setSettings(array $settings)
    {
        foreach ($settings as $key => $value) {
            $this->settingsModified[$key] = settings($key);
            Settings::set($key, $value);
        }

        Settings::save();

        $this->refreshAppAndExecuteCallbacks();
    }

    /**
     * @param array $attributes
     * @param null $guard
     * @return mixed
     */
    protected function createAndLoginUser(array $attributes = [], $guard = null)
    {
        $user = $this->createUser($attributes);

        $user = $this->setRoleForUser($user, 'User');

        $this->be($user, $guard);

        return $user;
    }

    /**
     * @param array $attributes
     * @param null $guard
     * @return mixed
     */
    protected function createAndLoginAdminUser(array $attributes = [], $guard = null)
    {
        $user = $this->createUser($attributes);

        $user = $this->setRoleForUser($user, 'Admin');

        $this->be($user, $guard);

        return $user;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    protected function createUser(array $attributes = [])
    {
        return factory(User::class)->create($attributes);
    }

    /**
     * @return mixed
     */
    protected function createSuperUser()
    {
        $user = factory(User::class)->create();
        return $this->makeSuperUser($user);
    }

    protected function makeSuperUser(User $user = null)
    {
        $user = m::mock($user ?: User::class)->makePartial();
        $user->shouldReceive('hasPermission')->andReturn(true);

        return $user;
    }

    /**
     * @param User $user
     * @param $role
     * @return User
     */
    public function setRoleForUser(User $user, $role)
    {
        $role = Role::where('name', $role)->first();
        $user->setRole($role);

        return $user;
    }

    /**
     * @param User $user
     * @param $permission
     */
    public function addPermissionForUser(User $user, $permission)
    {
        $permissions = (array) $permission;

        foreach ($permissions as $perm) {
            $permObj = Permission::firstOrCreate(['name' => $perm]);
            $user->role->attachPermission($permObj);
        }
    }

    public function seeInTable($selector, $text, $rowNumber, $columnNumber, $negate = false)
    {
        $fullSelector = "{$selector} tbody tr:nth-child({$rowNumber}) > td:nth-child({$columnNumber})";
        return $this->seeInElement($fullSelector, $text, $negate);
    }

    public function dontSeeInTable($selector, $text, $rowNumber, $columnNumber)
    {
        return $this->seeInTable($selector, $text, $rowNumber, $columnNumber, true);
    }

    /**
     * Click on link that matches provided selector.
     *
     * @param $selector
     * @return $this
     */
    protected function clickOn($selector)
    {
        $link = $this->crawler->filter($selector)->first();
        return $this->visit($link->link()->getUri());
    }

    protected function refreshApp()
    {
        $this->refreshApplication();

        if ($this->isSQLiteConnection()) {
            $this->executeCallbacks();
        }
    }
}

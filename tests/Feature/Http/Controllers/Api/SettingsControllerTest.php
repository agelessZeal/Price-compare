<?php

namespace Tests\Feature\Http\Controllers\Api;

use Settings;
use Tests\Feature\ApiTestCase;
use Vanguard\User;

class SettingsControllerTest extends ApiTestCase
{
    public function test_unauthenticated()
    {
        $this->getJson('/api/settings')
            ->assertResponseStatus(401);
    }

    public function test_get_settings_without_permission()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api')
            ->getJson('/api/settings')
            ->assertResponseStatus(403);
    }

    public function test_get_settings()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'settings.general');

        $this->getJson("/api/settings");

        $this->assertResponseOk()
            ->seeJsonEquals(Settings::all());
    }
}

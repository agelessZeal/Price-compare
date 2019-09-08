<?php

namespace Tests\Feature\Http\Controllers\Api\Users;

use Illuminate\Http\UploadedFile;
use Tests\Feature\ApiTestCase;
use Vanguard\Events\User\UpdatedByAdmin;
use Vanguard\User;

class AvatarControllerTest extends ApiTestCase
{
    public function test_upload_user_avatar_unauthenticated()
    {
        $user = factory(User::class)->create();

        $server = $this->transformHeadersToServerVars([
            'Accept' => 'application/json',
            'Content-Type' => 'image/jpeg'
        ]);

        $this->call('PUT', "/api/users/{$user->id}/avatar", [], [], [], $server);

        $this->assertResponseStatus(401);
    }

    public function test_upload_avatar_without_permission()
    {
        $user = factory(User::class)->create();

        $server = $this->transformHeadersToServerVars([
            'Accept' => 'application/json',
            'Content-Type' => 'image/jpeg'
        ]);

        $this->actingAs($user, 'api')
            ->call('PUT', "/api/users/{$user->id}/avatar", [], [], [], $server);

        $this->assertResponseStatus(403);
    }

    public function test_upload_avatar_image()
    {
        $this->expectsEvents(UpdatedByAdmin::class);

        $user = $this->login();

        $this->addPermissionForUser($user, 'users.manage');

        $file = UploadedFile::fake()->image('avatar.png', 500, 500);

        $fileContent = file_get_contents($file->getRealPath());

        $server = $this->transformHeadersToServerVars([
            'Accept' => 'application/json',
            'Content-Type' => 'image/jpeg'
        ]);

        $this->call('PUT', "/api/users/{$user->id}/avatar", [], [], [], $server, $fileContent);

        $response = $this->decodeResponseJson();

        $this->seeStatusCode(200)
            ->assertNotNull($response['avatar']);

        $uploadedFile = str_replace(url(''), '', $response['avatar']);
        $uploadedFile = public_path(ltrim($uploadedFile, "/"));

        $this->assertFileExists($uploadedFile);

        list($width, $height) = getimagesize($uploadedFile);

        $this->assertEquals(160, $width);
        $this->assertEquals(160, $height);

        @unlink($uploadedFile);
    }

    public function test_upload_invalid_image()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'users.manage');

        $file = UploadedFile::fake()->create('avatar.png', 500);

        $fileContent = file_get_contents($file->getRealPath());

        $server = $this->transformHeadersToServerVars([
            'Accept' => 'application/json',
            'Content-Type' => 'image/jpeg'
        ]);

        $this->call('PUT', "/api/users/{$user->id}/avatar", [], [], [], $server, $fileContent);

        $this->seeStatusCode(422)
            ->seeJsonContains([
                'file' => [
                    trans('validation.image', ['attribute' => 'file'])
                ]
            ]);
    }

    public function test_update_avatar_from_external_source()
    {
        $this->expectsEvents(UpdatedByAdmin::class);

        $user = $this->login();

        $this->addPermissionForUser($user, 'users.manage');

        $url = 'http://google.com';

        $this->putJson("/api/users/{$user->id}/avatar/external", [
            'url' => $url
        ]);

        $this->assertResponseOk();
        $this->seeJsonContains([
            'avatar' => $url
        ]);
    }

    public function test_update_avatar_with_invalid_external_source()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'users.manage');

        $this->putJson("/api/users/{$user->id}/avatar/external", [
            'url' => 'foo'
        ]);

        $this->assertResponseStatus(422);
    }

    public function test_delete_user_avatar()
    {
        $this->expectsEvents(UpdatedByAdmin::class);

        $user = $this->login();

        $this->addPermissionForUser($user, 'users.manage');

        $user->forceFill([
            'avatar' => 'http://google.com'
        ])->save();

        $this->deleteJson("api/users/{$user->id}/avatar")
            ->assertResponseOk()
            ->seeJsonContains([
                'avatar' => url('assets/img/profile.png') // default profile image
            ]);
    }
}

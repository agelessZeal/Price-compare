<?php

namespace Tests\Feature\Http\Controllers\Api\Profile;

use Illuminate\Http\UploadedFile;
use Tests\Feature\ApiTestCase;

class AvatarControllerTest extends ApiTestCase
{
    public function test_only_authenticated_user_can_update_avatar()
    {
        $this->putJson('/api/me/avatar');

        $this->assertResponseStatus(401);
    }

    public function test_upload_avatar_image()
    {
        $this->login();

        $file = UploadedFile::fake()->image('avatar.png', 500, 500);

        $fileContent = file_get_contents($file->getRealPath());

        $server = $this->transformHeadersToServerVars([
            'Accept' => 'application/json',
            'Content-Type' => 'image/jpeg'
        ]);

        $this->call('PUT', '/api/me/avatar', [], [], [], $server, $fileContent);

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
        $this->login();

        $file = UploadedFile::fake()->create('avatar.png', 500);

        $fileContent = file_get_contents($file->getRealPath());

        $server = $this->transformHeadersToServerVars([
            'Accept' => 'application/json',
            'Content-Type' => 'image/jpeg'
        ]);

        $this->call('PUT', '/api/me/avatar', [], [], [], $server, $fileContent);

        $this->seeStatusCode(422)
            ->seeJsonContains([
                'file' => [
                    trans('validation.image', ['attribute' => 'file'])
                ]
            ]);
    }

    public function test_update_avatar_from_external_source()
    {
        $this->login();

        $url = 'http://google.com';

        $this->putJson('/api/me/avatar/external', [
            'url' => $url
        ]);

        $this->assertResponseOk();
        $this->seeJsonContains([
            'avatar' => $url
        ]);
    }

    public function test_update_avatar_with_invalid_external_source()
    {
        $this->login();

        $this->putJson('/api/me/avatar/external', [
            'url' => 'foo'
        ]);

        $this->assertResponseStatus(422);
    }

    public function test_delete_avatar()
    {
        $user = $this->login();

        $user->forceFill([
            'avatar' => 'http://google.com'
        ])->save();

        $this->deleteJson("api/me/avatar")
            ->assertResponseOk()
            ->seeJsonContains([
                'avatar' => url('assets/img/profile.png') // default profile image
            ]);
    }
}

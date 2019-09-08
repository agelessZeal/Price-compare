<?php

namespace Tests\Feature\Http\Controllers\Api\Auth\Password;

use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Support\Str;
use Tests\Feature\ApiTestCase;
use Tests\MailTrap;
use Vanguard\User;

class ResetControllerTest extends ApiTestCase
{
    use MailTrap;

    public function test_password_reset()
    {
        $this->setSettings(['forgot_password' => true]);

        $user = factory(User::class)->create([
            'email' => 'test@test.com'
        ]);

        $token = $this->createNewToken();

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        $this->resetPassword($token, $user->email);

        $this->assertResponseOk();

        $user = $user->fresh();

        $this->assertTrue(Hash::check('123123', $user->password));
    }

    public function test_password_reset_with_expired_token()
    {
        $this->setSettings(['forgot_password' => true]);

        $user = factory(User::class)->create([
            'email' => 'test@test.com'
        ]);

        $token = $this->createNewToken();

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()->subHours(2)
        ]);

        $this->resetPassword($token, $user->email);

        $this->seeJsonEquals([
            'error' => "This password reset token is invalid."
        ]);
    }

    public function test_password_reset_with_invalid_email()
    {
        $this->setSettings(['forgot_password' => true]);

        $user = factory(User::class)->create(['email' => 'test@test.com']);

        $token = $this->createNewToken();

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        $this->resetPassword($token, 'foo@bar.com');

        $this->seeJsonContains([
            'error' => "We can't find a user with that e-mail address."
        ]);
    }

    private function resetPassword($token, $email)
    {
        $this->postJson('api/password/reset', [
            'token' => $token,
            'email' => $email,
            'password' => '123123',
            'password_confirmation' => '123123'
        ]);
    }

    private function createNewToken()
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return hash_hmac('sha256', Str::random(40), $key);
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Api\Auth\Password;

use Carbon\Carbon;
use Tests\Feature\ApiTestCase;
use Tests\MailTrap;
use Vanguard\Events\User\RequestedPasswordResetEmail;
use Vanguard\Services\Auth\Api\Token;
use Vanguard\User;

class RemindControllerTest extends ApiTestCase
{
    use MailTrap;

    public function test_send_password_reminder()
    {
        factory(User::class)->create(['email' => 'test@test.com']);

//        $this->expectsEvents(RequestedPasswordResetEmail::class);

        $this->postJson('api/password/remind', [
            'email' => 'test@test.com'
        ]);

        $this->assertResponseOk();

        $message = $this->fetchInbox()[0];

        $this->assertEquals('test@test.com', $message['to_email']);
        $this->assertEquals(config('mail.from.address'), $message['from_email']);
        $this->assertEquals(config('mail.from.name'), $message['from_name']);
        $this->assertContains(
            trans('app.request_for_password_reset_made'),
            $message['html_body']
        );

        $this->assertContains(
            trans('app.if_you_did_not_requested'),
            $message['html_body']
        );

        $this->emptyInbox();
    }

    public function test_password_reminder_with_wrong_email()
    {
        $this->postJson('api/password/remind', [
            'email' => 'test@test.com'
        ]);

        $this->assertResponseStatus(422)
            ->seeJsonEquals([
                'email' => ['The selected email is invalid.']
            ]);
    }
}

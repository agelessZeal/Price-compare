<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use Tests\Feature\ApiTestCase;
use Tests\MailTrap;
use Vanguard\User;

class RegistrationControllerTest extends ApiTestCase
{
    use MailTrap;

    public function test_register_user_when_registration_is_disabled()
    {
        $this->setSettings([
            'reg_enabled' => false
        ]);

        $this->postJson('api/register');

        $this->assertResponseStatus(404);
    }

    public function test_register_user()
    {
        $this->setSettings([
            'reg_enabled' => true,
            'reg_email_confirmation' => false,
            'registration.captcha.enabled' => false,
            'tos' => false
        ]);

        $data = [
            'email' => 'john.doe@test.com',
            'username' => 'john.doe',
            'password' => '123123',
            'password_confirmation' => '123123'
        ];

        $this->postJson("/api/register", $data);

        $expected = array_except($data, ['password', 'password_confirmation']);

        $this->seeStatusCode(201)
            ->seeJson([
                'requires_email_confirmation' => false
            ])
            ->seeInDatabase('users', $expected);
    }

    public function test_register_user_with_email_confirmation()
    {
        $this->setSettings([
            'reg_enabled' => true,
            'reg_email_confirmation' => true,
            'registration.captcha.enabled' => false,
            'tos' => false
        ]);

        $data = [
            'email' => 'john.doe@test.com',
            'username' => 'john.doe',
            'password' => '123123',
            'password_confirmation' => '123123'
        ];

        $this->postJson("/api/register", $data);

        $expected = array_except($data, ['password', 'password_confirmation']);

        $this->seeStatusCode(201)
            ->seeJson([
                'requires_email_confirmation' => true
            ])
            ->seeInDatabase('users', $expected);

        $token = User::where('email', $data['email'])->first()->confirmation_token;

        $message = $this->fetchInbox()[0];

        $this->assertEquals($data['email'], $message['to_email']);
        $this->assertEquals(config('mail.from.address'), $message['from_email']);
        $this->assertEquals(config('mail.from.name'), $message['from_name']);
        $this->assertContains(
            trans('app.thank_you_for_registering', ['app' => settings('app_name')]),
            trim($message['html_body'])
        );
        $this->assertContains(
            trans('app.confirm_email_on_link_below'),
            trim($message['html_body'])
        );
        $this->assertContains(
            route('register.confirm-email', $token),
            trim($message['html_body'])
        );

        $this->emptyInbox();
    }

    public function test_register_with_tos()
    {
        $this->setSettings([
            'reg_enabled' => true,
            'reg_email_confirmation' => false,
            'registration.captcha.enabled' => false,
            'tos' => true
        ]);

        $data = [
            'email' => 'john.doe@test.com',
            'username' => 'john.doe',
            'password' => '123123',
            'password_confirmation' => '123123'
        ];

        $this->postJson("/api/register", $data);

        $this->seeStatusCode(422)
            ->seeJson([
                'tos' => [
                    trans('app.you_have_to_accept_tos')
                ]
            ]);
    }
}

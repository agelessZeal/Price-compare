<?php

namespace Vanguard\Listeners\Registration;

use Vanguard\Events\User\Registered;
use Vanguard\Notifications\EmailConfirmation;
use Vanguard\Repositories\User\UserRepository;

class SendConfirmationEmail
{
    /**
     * @var UserRepository
     */
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        if (! settings('reg_email_confirmation')) {
            return;
        }

        $user = $event->getRegisteredUser();

        $token = str_random(60);
        $this->users->update($user->id, [
            'confirmation_token' => $token
        ]);

        $user->notify(new EmailConfirmation($token));
    }
}

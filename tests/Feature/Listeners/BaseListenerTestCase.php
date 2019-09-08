<?php

namespace Tests\Feature\Listeners;

use Input;
use Tests\Feature\FunctionalTestCase;
use Vanguard\User;

class BaseListenerTestCase extends FunctionalTestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->be($this->user);
    }

    protected function assertMessageLogged($msg, $user = null)
    {
        $this->seeInDatabase('user_activity', [
            'user_id' => $user ? $user->id : $this->user->id,
            'ip_address' => Input::ip(),
            'user_agent' => Input::header('User-agent'),
            'description' => $msg
        ]);
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Api;

use Carbon\Carbon;
use Tests\Feature\ApiTestCase;
use Tests\Feature\FunctionalTestCase;
use Vanguard\Repositories\Session\SessionRepository;
use Vanguard\Transformers\SessionTransformer;
use Vanguard\User;

class SessionsControllerTest extends ApiTestCase
{
    public function setUp()
    {
        parent::setUp();

        config(['session.driver' => 'database']);
    }

    public function test_unauthenticated()
    {
        $user = factory(User::class)->create();

        $session = $this->createSession($user);

        $this->getJson("/api/sessions/{$session->id}")
            ->assertResponseStatus(401);
    }

    public function test_get_session_which_belongs_to_other_user()
    {
        $user = $this->login();
        $user2 = factory(User::class)->create();

        $session = $this->createSession($user2);

        $this->getJson("/api/sessions/{$session->id}")
            ->assertResponseStatus(403);
    }

    public function test_get_session()
    {
        $user = $this->login();

        $session = $this->createSession($user);

        $this->getJson("/api/sessions/{$session->id}");

        $this->seeStatusCode(200)
            ->seeJson(
                (new SessionTransformer)->transform($session)
            );
    }

    public function test_invalidate_his_own_session()
    {
        $user = $this->login();

        $session = $this->createSession($user);

        $this->deleteJson("/api/sessions/{$session->id}")
            ->seeStatusCode(200)
            ->seeJson([
                'success' => true
            ]);
    }

    public function test_invalidate_session_for_other_user()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'users.manage');

        $user2 = factory(User::class)->create();

        $session = $this->createSession($user2);

        $this->deleteJson("/api/sessions/{$session->id}")
            ->seeStatusCode(200)
            ->seeJson([
                'success' => true
            ]);
    }

    public function test_invalidate_session_for_other_user_without_permission()
    {
        $user = $this->login();

        $user2 = factory(User::class)->create();

        $session = $this->createSession($user2);

        $this->deleteJson("/api/sessions/{$session->id}")
            ->seeStatusCode(403);
    }

    private function createSession(User $user)
    {
        $sessionId = str_random(40);

        $data = [
            'id' => $sessionId,
            'user_id' => $user->id,
            'ip_address' => "127.0.0.1",
            'user_agent' => 'foo',
            'payload' => str_random(),
            'last_activity' => Carbon::now()->timestamp
        ];

        \DB::table('sessions')->insert($data);

        return app(SessionRepository::class)->find($sessionId);
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Api\Users;

use Tests\Feature\ApiTestCase;
use Vanguard\Repositories\Session\SessionRepository;
use Vanguard\Transformers\SessionTransformer;
use Vanguard\User;

class SessionsControllerTest extends ApiTestCase
{
    public function test_get_sessions_unauthenticated()
    {
        $user = factory(User::class)->create();

        $this->getJson("/api/users/{$user->id}/sessions")
            ->seeStatusCode(401);
    }

    public function test_get_user_sessions_without_permission()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api')
            ->getJson("/api/users/{$user->id}/sessions")
            ->seeStatusCode(403);
    }

    public function test_get_user_sessions()
    {
        putenv('SESSION_DRIVER=database');

        $this->refreshApp();

        $user = $this->login();

        $this->addPermissionForUser($user, 'users.manage');

        $sessions = $this->generateNonExpiredSessions($user);

        $this->getJson("/api/users/{$user->id}/sessions")
            ->seeStatusCode(200)
            ->seeJson(
                $this->transformCollection($sessions, new SessionTransformer)
            );
    }

    private function generateNonExpiredSessions(User $user, $count = 5)
    {
        $sessions = [];
        $faker = $this->app->make(\Faker\Generator::class);
        $lifetime = config('session.lifetime') - 1;

        for ($i = 0; $i < $count; $i++) {
            array_push($sessions, [
                'id' => str_random(40),
                'user_id' => $user->id,
                'ip_address' => $faker->ipv4,
                'user_agent' => $faker->userAgent,
                'payload' => str_random(),
                'last_activity' => $faker->dateTimeBetween("-{$lifetime} minutes")->getTimestamp()
            ]);
        }

        \DB::table('sessions')->insert($sessions);

        return app(SessionRepository::class)->getUserSessions($user->id);
    }
}

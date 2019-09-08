<?php

namespace Tests\Feature\Http\Controllers\Api\Profile;

use Carbon\Carbon;
use Tests\Feature\ApiTestCase;
use Vanguard\Repositories\Session\SessionRepository;
use Vanguard\Transformers\SessionTransformer;
use Vanguard\User;

class SessionsControllerTest extends ApiTestCase
{
    public function test_get_user_sessions_unauthenticated()
    {
        $this->getJson('/api/me/sessions')
            ->assertResponseStatus(401);
    }

    public function test_get_sessions_if_non_database_driver_is_used()
    {
        putenv('SESSION_DRIVER=array');

        $this->refreshApp();

        $this->login();

        $this->getJson('/api/me/sessions')
            ->assertResponseStatus(404);
    }

    public function test_get_user_sessions()
    {
        putenv('SESSION_DRIVER=database');

        $this->refreshApp();

        $user = $this->login();

        $sessions = $this->generateNonExpiredSessions($user);

        $this->getJson('/api/me/sessions')
            ->assertResponseOk()
            ->seeJson(
                $this->transformCollection(collect($sessions), new SessionTransformer)
            );
    }

    private function generateNonExpiredSessions(User $user, $count = 5)
    {
        $sessions = [];
        $faker = $this->app->make(\Faker\Generator::class);

        for ($i = 0; $i < $count; $i++) {
            array_push($sessions, [
                'id' => str_random(40),
                'user_id' => $user->id,
                'ip_address' => $faker->ipv4,
                'user_agent' => $faker->userAgent,
                'payload' => str_random(),
                'last_activity' => Carbon::now()->subMinute()->timestamp
            ]);
        }

        \DB::table('sessions')->insert($sessions);

        return app(SessionRepository::class)->getUserSessions($user->id);
    }
}

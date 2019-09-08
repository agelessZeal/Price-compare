<?php

namespace Tests\Feature\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\Feature\ApiTestCase;
use Tests\Feature\FunctionalTestCase;
use Vanguard\Country;
use Vanguard\Repositories\Activity\ActivityRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Role;
use Vanguard\Services\Logging\UserActivity\Activity;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\Transformers\ActivityTransformer;
use Vanguard\Transformers\RoleTransformer;
use Vanguard\Transformers\SessionTransformer;
use Vanguard\Transformers\UserTransformer;
use Vanguard\User;

class StatsControllerTest extends ApiTestCase
{
    public function test_unauthenticated()
    {
        $this->getJson('/api/stats')
            ->assertResponseStatus(401);
    }

    public function test_get_stats_as_admin()
    {
        $adminRole = Role::whereName('Admin')->first();

        $user = factory(User::class)->create([
            'role_id' => $adminRole->id,
        ]);

        $this->be($user, 'api');

        Carbon::setTestNow(Carbon::now()->startOfYear());

        $activeUsers = factory(User::class)->times(4)->create([
            'status' => UserStatus::ACTIVE
        ]);

        Carbon::setTestNow(null);

        $bannedUsers = factory(User::class)->times(2)->create([
            'status' => UserStatus::BANNED
        ]);

        $unconfirmedUsers = factory(User::class)->times(7)->create([
            'status' => UserStatus::UNCONFIRMED
        ]);

        $users = app(UserRepository::class);

        $this->getJson("/api/stats");

        $usersPerMonth = $users->countOfNewUsersPerMonth(
            Carbon::now()->startOfYear(),
            Carbon::now()
        );

        $latestRegistrations = $users->latest(7);

        $this->assertResponseOk()
            ->seeJsonContains([
                'users_per_month' => $usersPerMonth,
                'users_per_status' => [
                    'total' => 14,
                    'new' => $users->newUsersCount(),
                    'banned' => 2,
                    'unconfirmed' => 7
                ],
                'latest_registrations' => $this->transformCollection($latestRegistrations, new UserTransformer)
            ]);
    }

    public function test_get_stats_as_non_admin_user()
    {
        $user = factory(User::class)->create();

        $this->be($user, 'api');

        Carbon::setTestNow(Carbon::now()->subWeek());

        factory(Activity::class)->times(5)->create([
            'user_id' => $user->id
        ]);

        Carbon::setTestNow(null);

        factory(Activity::class)->times(5)->create([
            'user_id' => $user->id
        ]);

        $this->getJson("/api/stats");

        $expected = app(ActivityRepository::class)->userActivityForPeriod(
            $user->id,
            Carbon::now()->subWeek(2),
            Carbon::now()
        )->toArray();

        $this->assertResponseOk()
            ->seeJsonEquals($expected);
    }
}

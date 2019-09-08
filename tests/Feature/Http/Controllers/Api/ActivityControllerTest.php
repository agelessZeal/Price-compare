<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\Feature\ApiTestCase;
use Vanguard\Services\Logging\UserActivity\Activity;
use Vanguard\Transformers\ActivityTransformer;
use Vanguard\User;

class ActivityControllerTest extends ApiTestCase
{
    public function test_unauthenticated()
    {
        $this->getJson('/api/activity')
            ->assertResponseStatus(401);
    }

    public function test_get_activities_without_permission()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api')
            ->getJson('/api/activity')
            ->assertResponseStatus(403);
    }

    public function test_paginate_activities()
    {
        $user = $this->getUser();
        $user2 = factory(User::class)->create();

        $activities = factory(Activity::class)->times(25)->create([
            'user_id' => $user->id
        ]);

        factory(Activity::class)->times(10)->create([
            'user_id' => $user2->id
        ]);

        $this->getJson("/api/activity");

        $transformed = $this->transformCollection(
            $activities->take(20),
            new ActivityTransformer
        );

        $response = $this->decodeResponseJson();

        $this->assertEquals($response['data'], $transformed);
        $this->assertEquals($response['meta'], [
            'current_page' => 1,
            'from' => 1,
            'to' => 20,
            'last_page' => 2,
            'prev_page_url' => null,
            'next_page_url' => url("api/activity?page=2"),
            'total' => 35,
            'per_page' => 20
        ]);
    }

    public function test_paginate_activities_with_search_param()
    {
        $user = $this->getUser();

        $set1 = factory(Activity::class)->times(10)->create([
            'user_id' => $user->id,
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
        ]);

        $set2 = factory(Activity::class)->times(5)->create([
            'user_id' => $user->id,
            'description' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...'
        ]);

        $transformed = $this->transformCollection(
            $set2,
            new ActivityTransformer
        );

        $this->getJson("/api/activity?search=minim&per_page=10");

        $response = $this->decodeResponseJson();

        $this->assertEquals($response['data'], $transformed);
        $this->assertEquals($response['meta'], [
            'current_page' => 1,
            'from' => 1,
            'to' => 5,
            'last_page' => 1,
            'prev_page_url' => null,
            'next_page_url' => null,
            'total' => 5,
            'per_page' => 10
        ]);
    }

    public function test_paginate_activities_with_more_records_per_page_than_allowed()
    {
        $user = $this->getUser();

        $this->getJson("/api/activity?per_page=140");

        $this->seeStatusCode(422);
    }

    /**
     * @return mixed
     */
    private function getUser()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'users.activity');

        return $user;
    }
}

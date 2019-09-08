<?php

namespace Tests\Feature\Http\Controllers\Api\Users;

use Tests\Feature\ApiTestCase;
use Vanguard\Services\Logging\UserActivity\Activity;
use Vanguard\Transformers\ActivityTransformer;
use Vanguard\User;

class ActivityControllerTest extends ApiTestCase
{
    public function test_auth()
    {
        $user = factory(User::class)->create();

        $this->getJson("/api/users/{$user->id}/activity")
            ->assertResponseStatus(401);
    }

    public function test_cannot_view_user_activity_without_permission()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api')
            ->getJson("/api/users/{$user->id}/activity")
            ->assertResponseStatus(403);
    }

    public function test_paginate_activities_for_user()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'users.activity');

        $activities = factory(Activity::class)->times(25)->create([
            'user_id' => $user->id
        ]);

        $this->getJson("/api/users/{$user->id}/activity");

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
            'next_page_url' => url("api/users/{$user->id}/activity?page=2"),
            'total' => 25,
            'per_page' => 20
        ]);
    }

    public function test_paginate_activities_for_user_with_search_param()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'users.activity');

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

        $this->getJson("/api/users/{$user->id}/activity?search=minim&per_page=10");

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

    public function test_paginate_activities_for_user_with_more_activities_per_page_than_allowed()
    {
        $user = $this->login();

        $this->addPermissionForUser($user, 'users.activity');

        $this->getJson("/api/users/{$user->id}/activity?per_page=140");

        $this->seeStatusCode(422);
    }
}

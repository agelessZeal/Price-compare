<?php

namespace Tests\Feature\Http\Controllers\Api\Profile;

use Carbon\Carbon;
use Tests\Feature\ApiTestCase;
use Vanguard\Transformers\UserTransformer;

class DetailsControllerTest extends ApiTestCase
{
    public function test_get_user_profile_unauthenticated()
    {
        $this->getJson('/api/me')
            ->assertResponseStatus(401);
    }

    public function test_get_user_profile()
    {
        $user = $this->login();

        $transformed = (new UserTransformer)->transform($user);

        $this->getJson('/api/me')
            ->seeStatusCode(200)
            ->seeJson($transformed);
    }

    public function test_update_user_profile_unauthenticated()
    {
        $this->patchJson('/api/me/details')
            ->assertResponseStatus(401);
    }

    public function test_update_user_profile()
    {
        $user = $this->login();

        $data = $this->getData();

        $this->patchJson("/api/me/details", $data);

        $transformed = (new UserTransformer)->transform($user->fresh());

        $this->seeJson($transformed);

        $this->seeInDatabase('users', array_merge($data, [
            'id' => $user->id
        ]));
    }

    public function test_partially_update_user_details()
    {
        $user = $this->login();

        $data = [
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ];

        $this->patchJson("/api/me/details", $data);

        $transformed = (new UserTransformer)->transform($user->fresh());

        $this->seeJson($transformed);

        $this->seeInDatabase('users', array_merge($data, [
            'id' => $user->id,
            'birthday' => $user->birthday->format('Y-m-d'),
            'phone' => $user->phone,
            'address' => $user->address,
            'country_id' => $user->country_id,
        ]));
    }

    public function test_update_without_country_id()
    {
        $user = $this->login();

        $data = $this->getData();

        unset($data['country_id']);

        $this->patchJson("/api/me/details", $data);

        $transformed = (new UserTransformer)->transform($user->fresh());

        $this->seeJson($transformed);

        $this->seeInDatabase('users', array_merge($data, [
            'id' => $user->id
        ]));
    }

    public function test_update_with_invalid_date_format()
    {
        $this->login();

        $this->patchJson("/api/me/details", [
            'birthday' => 'foo'
        ]);

        $this->assertResponseStatus(422);

        $this->seeJson([
            'birthday' => [
                trans('validation.date', ['attribute' => 'birthday'])
            ]
        ]);
    }

    /**
     * @return array
     */
    private function getData(array $attrs = [])
    {
        return array_merge([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'birthday'   => Carbon::now()->subYears(25)->format('Y-m-d'),
            'phone'      => '(123) 456 789',
            'address'    => 'some address 1',
            'country_id' => 688,
        ], $attrs);
    }
}

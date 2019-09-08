<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\Feature\ApiTestCase;
use Vanguard\Country;
use Vanguard\Transformers\CountryTransformer;

class CountriesControllerTest extends ApiTestCase
{
    public function test_unauthenticated()
    {
        $this->getJson('/api/countries')
            ->assertResponseStatus(401);
    }

    public function test_get_all_countries()
    {
        $this->login();

        $this->getJson("/api/countries");

        $transformed = $this->transformCollection(
            Country::all(),
            new CountryTransformer
        );

        $this->assertResponseOk()
            ->seeJson($transformed);
    }
}

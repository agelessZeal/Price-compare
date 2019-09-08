<?php

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Vanguard\User;

class ApiTestCase extends FunctionalTestCase
{
    public function setUp()
    {
        putenv("EXPOSE_API=true");

        parent::setUp();
    }

    /**
     * @return mixed
     */
    protected function login()
    {
        $user = factory(User::class)->create();

        $this->be($user, 'api');

        return $user;
    }

    /**
     * @return mixed
     */
    protected function loginSuperUser()
    {
        $user = $this->createSuperUser();

        $this->be($user, 'api');

        return $user;
    }

    /**
     * Transform provided collection of items.
     * @param Collection $collection
     * @param $transformer
     * @return array
     */
    protected function transformCollection(Collection $collection, $transformer)
    {
        $transformed = [];

        foreach ($collection as $item) {
            $transformed[] = $transformer->transform($item);
        }

        return $transformed;
    }

    /**
     * Check if JWT token in response contains
     * specified jti claim.
     * @param $jti
     * @return $this
     */
    protected function assertJwtTokenContains($jti)
    {
        $response = $this->decodeResponseJson();

        $parts = explode(".", $response['token']);

        $claims = json_decode(base64_decode($parts[1]));

        $this->assertEquals($jti, $claims->jti);

        return $this;
    }
}

<?php

use Faker\Generator as Faker;

$factory->define(Vanguard\User::class, function (Faker $faker) {
    return [
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'phone' => $faker->phoneNumber,
        'avatar' => null,
        'address' => $faker->address,
        'country_id' => function () use ($faker) {
            return $faker->randomElement(Vanguard\Country::pluck('id')->toArray());
        },
        'role_id' => function () {
            return factory(\Vanguard\Role::class)->create()->id;
        },
        'status' => Vanguard\Support\Enum\UserStatus::ACTIVE,
        'birthday' => $faker->date()
    ];
});

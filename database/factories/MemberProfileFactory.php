<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\MemberProfile::class, function (Faker\Generator $faker) {
    return [
    	'gender' 		=> 'M',
        'birth_date'	=> $faker->date('Y-m-d'),
        'phone_number'	=> $faker->tollFreePhoneNumber,
        'secondary_contact'	=> $faker->tollFreePhoneNumber,
        'address'	=> $faker->address
    ];
});

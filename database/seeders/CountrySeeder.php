<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'United Kingdom', 'iso_code' => 'GB', 'dial_code' => '+44'],
            ['name' => 'United States', 'iso_code' => 'US', 'dial_code' => '+1'],
            ['name' => 'Canada', 'iso_code' => 'CA', 'dial_code' => '+1'],
            ['name' => 'Australia', 'iso_code' => 'AU', 'dial_code' => '+61'],
            ['name' => 'Germany', 'iso_code' => 'DE', 'dial_code' => '+49'],
            ['name' => 'France', 'iso_code' => 'FR', 'dial_code' => '+33'],
            ['name' => 'Netherlands', 'iso_code' => 'NL', 'dial_code' => '+31'],
            ['name' => 'Spain', 'iso_code' => 'ES', 'dial_code' => '+34'],
            ['name' => 'Italy', 'iso_code' => 'IT', 'dial_code' => '+39'],
            ['name' => 'China', 'iso_code' => 'CN', 'dial_code' => '+86'],
            ['name' => 'India', 'iso_code' => 'IN', 'dial_code' => '+91'],
            ['name' => 'Brazil', 'iso_code' => 'BR', 'dial_code' => '+55'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
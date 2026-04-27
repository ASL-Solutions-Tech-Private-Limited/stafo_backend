<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = json_decode(file_get_contents(storage_path() . '/countries.json'), true);
        foreach ($countries as &$country) {
            $country['status'] = ($country['name'] === 'India') ? '1' : '0';
        }
        Country::insert($countries);
    }
}
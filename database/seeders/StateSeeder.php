<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = json_decode(file_get_contents(storage_path() . '/countries.json'), true);

        # Assign India's country_id
        $countryStates = json_decode(file_get_contents(storage_path('/states.json')), true);
        $states = $countryStates['india'];

        $india = DB::table('countries')->where('name', 'India')->first();
     
        $indiaId = $india->id ?? null; 

        if (!$indiaId) {
            throw new \Exception("India not found in countries.json");
        }

        foreach ($states as &$state) {
            $state['status'] = '1'; 
            $state['country_id'] = $indiaId; 
        }
        State::insert($states);
    }
}

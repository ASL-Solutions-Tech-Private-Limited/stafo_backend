<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districtsData = json_decode(file_get_contents(storage_path('/cities.json')), true);
        $states = State::pluck('id', 'name'); // ['state_name' => state_id]

        $districts = [];
        foreach ($districtsData['india'] as $stateName => $districtNames) {
            $stateId = $states[$stateName] ?? null;
        
            if (!$stateId) {
                throw new \Exception("State '{$stateName}' not found in the states table");
            }
        
            foreach ($districtNames as $districtName) {
                $districts[] = [
                    'name' => $districtName,
                    'state_id' => $stateId,
                    'status' => '1',
                ];
            }
        }
        City::insert($districts);

    }
}

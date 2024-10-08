<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClubsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("clubs")->insert([
            [
                'name' => 'Liverpool',
                'attack' => 9,
                'defense' => 8
            ],
            [
                'name' => 'Manchester City',
                'attack' => 9,
                'defense' => 8
            ],
            [
                'name' => 'Arsenal',
                'attack' => 8,
                'defense' => 9
            ],
            [
                'name' => 'Manchester United',
                'attack' => 7,
                'defense' => 6
            ],
            [
                'name' => 'Crystal Palace',
                'attack' => 5,
                'defense' => 7
            ],
            [
                'name' => 'Ipswich',
                'attack' => 5,
                'defense' => 5
            ],
        ]);
    }
}

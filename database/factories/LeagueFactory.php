<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LeagueFactory extends Factory
{
    public function definition()
    {
        return [
            'ulid' => (string) Str::ulid(),
            'name' => $this->faker->company(),
            'week' => $this->faker->numberBetween(1, 38),
            'finished' => $this->faker->boolean(),
        ];
    }
}

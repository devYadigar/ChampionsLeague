<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LeagueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ulid' => $this->faker->password(maxLength:26),
            'name' => $this->faker->company(),
            'week' => $this->faker->numberBetween(1, 38), 
            'finished' => $this->faker->boolean(),
        ];
    }
}

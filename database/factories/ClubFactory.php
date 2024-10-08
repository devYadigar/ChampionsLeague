<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->city(),
            'attack' => $this->faker->numberBetween(1, 10),
            'defense' => $this->faker->numberBetween(1, 10),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $createdAt = $this->faker->dateTimeBetween('-3 months', '-2 months');

        return [
            'name' => $this->faker->name,
            'created_at' => $createdAt,
            'updated_at' => $createdAt
        ];
    }
}

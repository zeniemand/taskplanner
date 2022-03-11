<?php

namespace Database\Factories;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
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
            //'card_id' => Card::factory(),
            'card_id' => rand(1,10),
            'created_at' => $createdAt,
            'updated_at' => $createdAt
        ];
    }
}

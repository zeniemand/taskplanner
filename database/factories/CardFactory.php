<?php

namespace Database\Factories;

use App\Models\DeskList;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
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
            //'desk_list_id' => DeskList::factory(),
            'desk_list_id' => rand(1,10),
            'created_at' => $createdAt,
            'updated_at' => $createdAt
        ];
    }
}

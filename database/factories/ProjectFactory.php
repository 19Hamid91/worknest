<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\Status;

class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeThisYear();
        $endDate = $this->faker->dateTimeBetween($startDate, '+2 months');

        return [
            'name' => $this->faker->words(3, true),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(Status::values()),
            'description' => $this->faker->paragraph(rand(5, 7)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

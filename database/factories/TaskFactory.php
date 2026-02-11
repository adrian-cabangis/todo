<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class TaskFactory extends Factory
{
    protected $model = \App\Models\Task::class;

    public function definition()
    {
        $statuses = ['ongoing', 'pending', 'completed', 'cancelled'];
        $priorities = ['low', 'medium', 'high'];

        return [
            'user_id' => User::factory(), // creates a user if none exists
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'deadline' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement($statuses),
            'priority' => $this->faker->randomElement($priorities),
        ];
    }
}

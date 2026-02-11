<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create 10 users, each with 3 tasks
        User::factory(10)->has(Task::factory()->count(3))->create();

        // Or, if you want additional random tasks:
        // Task::factory(20)->create();
    }
}

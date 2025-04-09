<?php

namespace database\seeders\Api;

use App\Models\Api\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::factory()->count(500)->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Schedule;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::factory(10)->create();

        User::factory()->hasAttached(
            Company::factory(5)
            ->hasAttached(User::factory(5), ['role' => 'employee'])
            ->has(Branch::factory(5)),
            ['role' => 'admin']
        )->create();

        foreach (Branch::all() as $branch) {
            Task::factory(5)->for($branch)
            ->sequence(fn ($sequence) => ['user_id' => $branch->company->employees()->get()->random()->id])
            ->has(Schedule::factory())
            ->create();
        }
    }
}

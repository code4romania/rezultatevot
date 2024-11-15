<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Election;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(['email' => 'admin@example.com'])
            ->admin()
            ->create();

        User::factory()
            ->count(10)
            ->create();

        Election::factory()
            ->count(10)
            ->create();

        Election::factory()
            ->live()
            // ->withLocalTurnout()
            // ->withAbroadTurnout()
            // ->withNationalRecords()
            // ->withDiasporaRecords()
            ->create();

        Artisan::call('scout:rebuild');
    }
}

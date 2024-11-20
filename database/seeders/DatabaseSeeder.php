<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Election;
use App\Models\Page;
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
            ->withArticles()
            ->count(10)
            ->create();

        Election::factory()
            ->live()
            ->withArticles()
            // ->withLocalTurnout()
            // ->withAbroadTurnout()
            // ->withNationalRecords()
            // ->withDiasporaRecords()
            ->create();

        Page::factory()
            ->count(10)
            ->create();

        Artisan::call('scout:rebuild');
    }
}

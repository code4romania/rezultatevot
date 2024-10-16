<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Election;
use App\Models\ElectionType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        $electionTypes = ElectionType::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Alegeri prezidențiale'],
                ['name' => 'Alegeri parlamentare'],
                ['name' => 'Alegeri europarlamentare'],
                ['name' => 'Alegeri locale'],
                ['name' => 'Referendum'],
            )
            ->create();

        Election::factory()
            ->count(10)
            ->recycle($electionTypes)
            ->create();
    }
}

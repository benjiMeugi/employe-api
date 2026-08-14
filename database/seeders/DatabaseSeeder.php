<?php

namespace Database\Seeders;

use App\Models\Employe;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    //use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            TitleSeeder::class,
            ClassificationSeeder::class,
            PositionSeeder::class,
            EmployeSeeder::class,
            UnitSeeder::class,
            CareerEventSeeder::class,
        ]);

        Employe::factory()->count(100)->create();
    }
}

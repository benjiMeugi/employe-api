<?php

namespace Database\Seeders;

use App\Models\Title;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            [
                'label' => 'Monsieur',
                'code' => 'M',
            ],
            [
                'label' => 'Madame',
                'code' => 'MME',
            ],
            [
                'label' => 'Professeur',
                'code' => 'PROF',
            ],
            [
                'label' => 'Docteur',
                'code' => 'DR',
            ],
            [
                'label' => 'Maître',
                'code' => 'MAITRE',
            ],
        ];

        foreach ($titles as $title) {
            Title::updateOrCreate(
                ['code' => $title['code']],
                $title
            );
        }
    }
}

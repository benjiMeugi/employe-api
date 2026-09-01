<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Seeder;

class ContractTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'CDI',
                'label' => 'Contrat à Durée Indéterminée',
                'is_fixed_term' => false,
                'max_duration_months' => null,
            ],
            [
                'code' => 'CDD',
                'label' => 'Contrat à Durée Déterminée',
                'is_fixed_term' => true,
                'max_duration_months' => 24,
            ],
            [
                'code' => 'STAGE',
                'label' => 'Contrat de stage',
                'is_fixed_term' => true,
                'max_duration_months' => 6,
            ],
            [
                'code' => 'INT',
                'label' => 'Contrat d\'intérim',
                'is_fixed_term' => true,
                'max_duration_months' => 18,
            ],
            [
                'code' => 'PERM',
                'label' => 'Contrat Permanent',
                'is_fixed_term' => false,
                'max_duration_months' => null,
            ],
        ];

        foreach ($types as $type) {
            ContractType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }

        $this->command->info('Types de contrat créés avec succès');
    }
}

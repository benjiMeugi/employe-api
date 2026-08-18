<?php

namespace Database\Seeders;

use App\Models\AbsenceType;
use Illuminate\Database\Seeder;

class AbsenceTypeSeeder extends Seeder
{
    public function run(): void
    {
        AbsenceType::create([
            'code' => 'CA',
            'label' => 'Congé annuel',
            'is_paid' => true,
            'is_cumulative' => true,
            'max_cumulative_years' => 2,
            'day_cap' => 30,
            'expiration_delay_months' => 3,
            'requires_supporting_document' => false,
        ]);

        AbsenceType::create([
            'code' => 'CM',
            'label' => 'Congé maladie',
            'is_paid' => true,
            'is_cumulative' => false,
            'max_cumulative_years' => null,
            'day_cap' => null,
            'expiration_delay_months' => null,
            'requires_supporting_document' => true,
        ]);

        AbsenceType::create([
            'code' => 'PERM',
            'label' => 'Permission exceptionnelle',
            'is_paid' => true,
            'is_cumulative' => false,
            'max_cumulative_years' => null,
            'day_cap' => 3,
            'expiration_delay_months' => null,
            'requires_supporting_document' => false,
        ]);

        AbsenceType::create([
            'code' => 'CSS',
            'label' => 'Congé sans solde',
            'is_paid' => false,
            'is_cumulative' => false,
            'max_cumulative_years' => null,
            'day_cap' => null,
            'expiration_delay_months' => null,
            'requires_supporting_document' => false,
        ]);

        AbsenceType::create([
            'code' => 'MAT',
            'label' => 'Congé maternité',
            'is_paid' => true,
            'is_cumulative' => false,
            'max_cumulative_years' => null,
            'day_cap' => 98,
            'expiration_delay_months' => null,
            'requires_supporting_document' => true,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Employe;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employe::orderBy('id')->get();

        $cdi = ContractType::where('code', 'CDI')->firstOrFail();
        $cdd = ContractType::where('code', 'CDD')->firstOrFail();
        $stage = ContractType::where('code', 'STAGE')->firstOrFail();

        /*
         * Employé 1 : CDI actif
         */
        if (isset($employees[0])) {
            Contract::updateOrCreate(
                ['employee_id' => $employees[0]->id],
                [
                    'contract_type_id' => $cdi->id,
                    'start_date' => '2024-01-15',
                    'end_date' => null,
                    'pay_frequency' => 'Monthly',
                    'base_salary' => 650000,
                    'status' => 'Active',
                ]
            );
        }

        /*
         * Employé 2 : ancien CDD terminé
         */
        if (isset($employees[1])) {
            Contract::updateOrCreate(
                ['employee_id' => $employees[1]->id],
                [
                    'contract_type_id' => $cdd->id,
                    'start_date' => '2012-01-09',
                    'end_date' => '2014-01-09',
                    'pay_frequency' => 'Monthly',
                    'base_salary' => 550000,
                    'status' => 'Terminated',
                ]
            );
        }

        /*
         * Employé 3 : CDI suspendu
         */
        if (isset($employees[2])) {
            Contract::updateOrCreate(
                ['employee_id' => $employees[2]->id],
                [
                    'contract_type_id' => $cdi->id,
                    'start_date' => '2021-06-01',
                    'end_date' => null,
                    'pay_frequency' => 'Monthly',
                    'base_salary' => 800000,
                    'status' => 'Suspended',
                ]
            );
        }

        /*
         * Employé 4 : ancien stage terminé
         */
        if (isset($employees[3])) {
            Contract::updateOrCreate(
                ['employee_id' => $employees[3]->id],
                [
                    'contract_type_id' => $stage->id,
                    'start_date' => '2025-01-06',
                    'end_date' => '2025-07-06',
                    'pay_frequency' => 'Monthly',
                    'base_salary' => 150000,
                    'status' => 'Terminated',
                ]
            );
        }

        /*
         * Employés suivants : CDI actifs
         */
        foreach ($employees->slice(4, 6) as $employee) {
            Contract::updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'contract_type_id' => $cdi->id,
                    'start_date' => now()->subYears(rand(1, 5))->toDateString(),
                    'end_date' => null,
                    'pay_frequency' => 'Monthly',
                    'base_salary' => rand(450, 120) * 5000,
                    'status' => 'Active',
                ]
            );
        }

        $this->command->info('Contrats de test créés avec succès.');
    }
}

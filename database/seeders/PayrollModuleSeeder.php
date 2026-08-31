<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Employe;
use App\Models\Title;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PayrollModuleSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('classification_payroll_line_type')->truncate();
        DB::table('payslip_lines')->truncate();
        DB::table('payslips')->truncate();
        DB::table('contracts')->truncate();
        DB::table('contract_types')->truncate();

        $cdi = ContractType::create([
            'code' => 'CDI',
            'label' => 'Contrat à Durée Indéterminée',
            'is_fixed_term' => false
        ]);

        ContractType::create([
            'code' => 'CDD',
            'label' => 'Contrat à Durée Déterminée',
            'is_fixed_term' => true,
            'max_duration_months' => 12
        ]);

        $employeeId = DB::table('employes')->value('id');

        if (!$employeeId) {
            $title = Title::firstOrCreate(
                ['code' => 'EMPLOYEE'],
                ['label' => 'Employee']
            );

            $employee = Employe::create([
                'registration_number' => 'EMP-000001',
                'first_name' => 'Payroll',
                'last_name' => 'Test',
                'birth_date' => '1990-01-01',
                'gender' => 'M',
                'hire_date' => '2026-01-01',
                'status' => true,
                'title_id' => $title->id,
            ]);

            $employeeId = $employee->id;
        }

        Contract::create([
            'employee_id' => $employeeId,
            'contract_type_id' => $cdi->id,
            'start_date' => '2026-01-01',
            'pay_frequency' => 'Monthly',
            'base_salary' => 3000.00,
            'status' => 'Active'
        ]);

        Schema::enableForeignKeyConstraints();
    }
}

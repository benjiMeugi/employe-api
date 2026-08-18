<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractType;
use App\Models\AbsenceType;
use App\Models\AbsenceRequest;
use App\Models\LeaveBalance;
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
        DB::table('absence_requests')->truncate();
        DB::table('absence_types')->truncate();
        DB::table('leave_balances')->truncate();

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

        $cp = AbsenceType::create([
            'code' => 'CP',
            'label' => 'Congés Payés',
            'is_paid' => true,
            'is_cumulative' => true
        ]);

        $css = AbsenceType::create([
            'code' => 'CSS',
            'label' => 'Congé Sans Solde',
            'is_paid' => false,
            'is_cumulative' => false
        ]);

        // CORRECTION CHIRURGICALE : 'employes' au lieu de 'employees'
        $employeeId = DB::table('employes')->value('id');

        if (!$employeeId) {
            $employeeId = 1;
        }

        Contract::create([
            'employee_id' => $employeeId,
            'contract_type_id' => $cdi->id,
            'start_date' => '2026-01-01',
            'pay_frequency' => 'Monthly',
            'base_salary' => 3000.00,
            'status' => 'Active'
        ]);

        LeaveBalance::create([
            'employee_id' => $employeeId,
            'absence_type_id' => $cp->id,
            'year' => 2026,
            'cumulative_acquired_days' => 30.00,
            'consumed_days' => 0.00,
            'expired_days' => 0.00,
            'available_balance' => 30.00
        ]);

        AbsenceRequest::create([
            'employee_id' => $employeeId,
            'absence_type_id' => $css->id,
            'requested_start_date' => '2026-08-10',
            'requested_end_date' => '2026-08-12',
            'requested_days_count' => 3,
            'reason' => 'Déménagement personnel',
            'status' => 'Approved',
            'is_deductible' => true
        ]);

        Schema::enableForeignKeyConstraints();
    }
}

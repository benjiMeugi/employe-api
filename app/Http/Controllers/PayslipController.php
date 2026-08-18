<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Payslip;
use App\Models\PayslipLine;
use App\Models\AbsenceRequest;
use App\Models\PayrollLineType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayslipController extends Controller
{
    public function index()
    {
        return response()->json(Payslip::with('lines')->get(), 200);
    }

    public function generate(Request $request)
    {
        $request->validate([
    // CORRECTION ICI : employes (français) au lieu de employees (anglais)
    'employee_id' => 'required|exists:employes,id', 
    'period' => 'required|date_format:Y-m',
]);


        $employeeId = $request->employee_id;
        $period = $request->period;

        $contract = Contract::where('employee_id', $employeeId)->where('status', 'Active')->first();
        if (!$contract) {
            return response()->json(['error' => 'Aucun contrat actif trouvé.'], 422);
        }

        $existing = Payslip::where('employee_id', $employeeId)->where('period', $period)->first();
        if ($existing) {
            return response()->json(['error' => 'Un bulletin existe déjà pour cette période.'], 422);
        }

        return DB::transaction(function () use ($employeeId, $contract, $period) {
            $baseSalary = $contract->base_salary;

            $payslip = Payslip::create([
                'employee_id' => $employeeId,
                'contract_id' => $contract->id,
                'period' => $period,
                'issue_date' => Carbon::now()->toDateString(),
                'status' => 'Pending'
            ]);

            $totalEarnings = $baseSalary;
            $totalDeductions = 0;

            // Règle de calcul des absences déductibles approuvées sur la période
            $start = Carbon::parse($period . '-01')->startOfMonth();
            $end = Carbon::parse($period . '-01')->endOfMonth();

            $absenceDays = AbsenceRequest::where('employee_id', $employeeId)
                ->where('status', 'Approved')
                ->where('is_deductible', true)
                ->where(function($q) use ($start, $end) {
                    $q->whereBetween('requested_start_date', [$start, $end])
                      ->orWhereBetween('requested_end_date', [$start, $end]);
                })->sum('requested_days_count');

            if ($absenceDays > 0) {
                $lineType = PayrollLineType::firstOrCreate(
                    ['code' => 'ABS'],
                    ['label' => 'Retenue Absence', 'nature' => 'Deduction', 'calculation_mode' => 'Formula']
                );

                $amount = ($baseSalary / 30) * $absenceDays;

                PayslipLine::create([
                    'payslip_id' => $payslip->id,
                    'payroll_line_type_id' => $lineType->id,
                    'calculation_base' => $baseSalary,
                    'rate' => ($absenceDays / 30) * 100,
                    'amount' => $amount
                ]);

                $totalDeductions += $amount;
            }

            $payslip->update([
                'gross_salary' => $totalEarnings,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'net_pay' => $totalEarnings - $totalDeductions,
            ]);

            return response()->json($payslip->load('lines'), 201);
        });
    }

    public function show($id)
    {
        $payslip = Payslip::with('lines.payrollLineType')->find($id);
        return $payslip ? response()->json($payslip, 200) : response()->json(['message' => 'Introuvable'], 404);
    }
}

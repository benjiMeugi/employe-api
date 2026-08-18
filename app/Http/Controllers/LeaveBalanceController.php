<?php

namespace App\Http\Controllers;

use App\Models\LeaveBalance;

class LeaveBalanceController extends Controller
{
    public function show($employee_id)
    {
        $balances = LeaveBalance::where('employee_id', $employee_id)->get();
        return response()->json($balances, 200);
    }
}

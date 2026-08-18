<?php

namespace App\Http\Controllers;

use App\Models\LeaveCredit;
use Illuminate\Http\Request;

class LeaveCreditController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'absence_type_id' => 'required|exists:absence_types,id',
            'period' => 'required|string',
            'acquired_days' => 'required|numeric',
            'acquisition_date' => 'required|date',
        ]);

        $credit = LeaveCredit::create($validated);
        return response()->json($credit, 201);
    }
}

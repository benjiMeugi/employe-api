<?php

namespace App\Http\Controllers;

use App\Models\PayrollLineType;
use Illuminate\Http\Request;

class PayrollLineTypeController extends Controller
{
    public function index()
    {
        return response()->json(PayrollLineType::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:payroll_line_types,code',
            'label' => 'required|string',
            'nature' => 'required|in:Earning,Deduction',
            'calculation_mode' => 'required|in:Rate,FixedAmount,Formula',
        ]);

        $type = PayrollLineType::create($validated);
        return response()->json($type, 201);
    }
}

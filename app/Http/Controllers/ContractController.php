<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        return response()->json(Contract::with('contractType')->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_type_id' => 'required|exists:contract_types,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'pay_frequency' => 'required|string',
            'base_salary' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Terminated,Suspended',
        ]);

        $contract = Contract::create($validated);
        return response()->json($contract, 201);
    }

    public function show($id)
    {
        $contract = Contract::with('contractType')->find($id);
        return $contract ? response()->json($contract, 200) : response()->json(['message' => 'Contrat introuvable'], 404);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ContractType;
use Illuminate\Http\Request;

class ContractTypeController extends Controller
{
    public function index()
    {
        return response()->json(ContractType::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:contract_types,code',
            'label' => 'required|string',
            'is_fixed_term' => 'required|boolean',
            'max_duration_months' => 'nullable|integer',
        ]);

        $type = ContractType::create($validated);
        return response()->json($type, 201);
    }

    public function show($id)
    {
        $type = ContractType::find($id);
        return $type ? response()->json($type, 200) : response()->json(['message' => 'Introuvable'], 404);
    }
}

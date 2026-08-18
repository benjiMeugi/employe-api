<?php

namespace App\Http\Controllers;

use App\Models\AbsenceType;
use Illuminate\Http\Request;

class AbsenceTypeController extends Controller
{
    public function index()
    {
        return response()->json(AbsenceType::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:absence_types,code',
            'label' => 'required|string',
            'is_paid' => 'required|boolean',
            'is_cumulative' => 'required|boolean',
        ]);

        $type = AbsenceType::create($validated);
        return response()->json($type, 201);
    }
}

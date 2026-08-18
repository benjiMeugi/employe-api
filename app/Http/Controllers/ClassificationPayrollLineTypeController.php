<?php

namespace App\Http\Controllers;

use App\Models\ClassificationPayrollLineType;
use Illuminate\Http\Request;

class ClassificationPayrollLineTypeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classification_id' => 'required|integer',
            'payroll_line_type_id' => 'required|exists:payroll_line_types,id',
            'value' => 'required|numeric',
        ]);

        // Crée ou met à jour la règle pour cette classification
        $pivot = ClassificationPayrollLineType::updateOrCreate(
            ['classification_id' => $validated['classification_id'], 'payroll_line_type_id' => $validated['payroll_line_type_id']],
            ['value' => $validated['value']]
        );

        return response()->json($pivot, 200);
    }
}

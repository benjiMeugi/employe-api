<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRequest;
use Illuminate\Http\Request;

class AbsenceRequestController extends Controller
{
    public function index()
    {
        return response()->json(AbsenceRequest::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'absence_type_id' => 'required|exists:absence_types,id',
            'requested_start_date' => 'required|date',
            'requested_end_date' => 'required|date|after_or_equal:requested_start_date',
            'requested_days_count' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        $validated['status'] = 'Pending';
        $absenceRequest = AbsenceRequest::create($validated);
        return response()->json($absenceRequest, 201);
    }
}

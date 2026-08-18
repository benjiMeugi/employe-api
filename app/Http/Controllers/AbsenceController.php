<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    /**
     * Liste toutes les absences réelles enregistrées.
     */
    public function index()
    {
        // On récupère les absences en chargeant la relation avec le type d'absence pour avoir plus de détails
        $absences = Absence::with('absenceType')->get();
        
        return response()->json($absences, 200);
    }

    /**
     * Enregistre manuellement une absence réelle dans la base de données.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'absence_type_id' => 'required|exists:absence_types,id',
            'absence_request_id' => 'nullable|exists:absence_requests,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days_count' => 'required|integer|min:1',
            'is_deductible' => 'required|boolean',
        ]);

        $absence = Absence::create($validated);

        return response()->json([
            'message' => 'Absence réelle enregistrée avec succès.',
            'data' => $absence->load('absenceType')
        ], 201);
    }

    /**
     * Affiche les détails d'une absence spécifique.
     */
    public function show($id)
    {
        $absence = Absence::with(['absenceType', 'absenceRequest'])->find($id);

        if (!$absence) {
            return response()->json(['message' => 'Absence introuvable.'], 404);
        }

        return response()->json($absence, 200);
    }
}

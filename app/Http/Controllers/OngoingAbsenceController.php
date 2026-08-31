<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Repository\Repository;
use App\Models\OngoingAbsence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Expose la vue des absences en cours : où en est-on dans une absence
 * précise — combien de jours écoulés, combien restants, date de retour.
 *
 * Rien à voir avec les soldes : ici on ne regarde ni octroi ni quota,
 * seulement la progression d'une absence entre ses dates et aujourd'hui.
 *
 * Vue calculée : aucune méthode d'écriture.
 */
class OngoingAbsenceController extends Controller
{
    /**
     * @var OngoingAbsence
     */
    private OngoingAbsence $model;

    /**
     * @var Repository
     */
    private Repository $repository;

    public function __construct()
    {
        $this->model = new OngoingAbsence();
        $this->repository = new Repository($this->model);
    }

    /**
     * Toutes les absences en cours, filtrables via query_ comme les
     * autres ressources.
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $query = $this->repository->parse_filters($request);

        if ($request->has('page') && $request->has('per_page')) {
            return $this->respondOk($query->paginate($request->input('per_page')));
        }
        return $this->respondOk($query->get());
    }

    /**
     * Les absences en cours d'un employé précis.
     *
     * @param int $employeeId
     */
    public function forEmployee(int $employeeId)
    {
        return $this->respondOk(
            OngoingAbsence::with('absenceType:id,code,label')
                ->where('employee_id', $employeeId)
                ->get()
        );
    }

    /**
     * L'absence en cours du user connecté — pour l'écran « où en
     * suis-je », sans avoir à connaître son propre id employé.
     *
     * @param Request $request
     */
    public function current(Request $request)
    {
        $userId = json_decode(Auth::token())->sub ?? null;

        $employee = \App\Models\Employe::where('user_id', $userId)->first();

        if (! $employee) {
            return $this->respondBadRequest([
                'employee' => "Aucun employé n'est rattaché à ce compte.",
            ]);
        }

        return $this->respondOk(
            OngoingAbsence::with('absenceType:id,code,label')
                ->where('employee_id', $employee->id)
                ->get()
        );
    }
}

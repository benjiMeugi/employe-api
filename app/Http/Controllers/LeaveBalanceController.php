<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Repository\Repository;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;

/**
 * Expose la vue des soldes de congé. Aucune méthode d'écriture :
 * store/update/delete n'existent volontairement pas.
 */
class LeaveBalanceController extends Controller
{
    /**
     * @var LeaveBalance
     */
    private LeaveBalance $model;

    /**
     * @var Repository
     */
    private Repository $repository;

    public function __construct()
    {
        $this->model = new LeaveBalance();
        $this->repository = new Repository($this->model);
    }

    /**
     * Tous les soldes, filtrables via query_ comme les autres
     * ressources — ex. query_={"where_employee_id":5}
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
     * Les soldes d'un employé, avec le libellé du type d'absence —
     * sans lui, le client ne reçoit qu'un absence_type_id, illisible
     * pour un humain.
     *
     * @param int $employeeId
     */
    public function forEmployee(int $employeeId)
    {
        return $this->respondOk(
            LeaveBalance::with('absenceType:id,code,label')
                ->where('employee_id', $employeeId)
                ->get()
        );
    }

    /**
     * Le solde du user connecté — ce que le frontend appelle pour
     * l'écran « mes congés », sans avoir à connaître son propre id
     * employé.
     *
     * @param Request $request
     */
    public function current(Request $request)
    {
        $userId = json_decode(\Illuminate\Support\Facades\Auth::token())->sub ?? null;

        $employee = \App\Models\Employe::where('user_id', $userId)->first();

        if (! $employee) {
            return $this->respondBadRequest([
                'employee' => "Aucun employé n'est rattaché à ce compte.",
            ]);
        }

        return $this->respondOk(
            LeaveBalance::with('absenceType:id,code,label')
                ->where('employee_id', $employee->id)
                ->get()
        );
    }

}

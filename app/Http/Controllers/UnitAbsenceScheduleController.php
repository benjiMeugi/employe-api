<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Repository\Repository;
use App\Models\UnitAbsenceSchedule;
use App\Models\UnitStaffing;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Expose le calendrier des absences par unité. Aucune méthode
 * d'écriture : store/update/delete n'existent volontairement pas.
 *
 * Une absence se crée par le module absence, une demande par le
 * module demande. Ici on ne fait que lire pour décider.
 */
class UnitAbsenceScheduleController extends Controller
{
    /**
     * Au-delà, le découpage jour par jour devient coûteux pour une
     * information que personne ne lit d'un coup d'œil.
     */
    private const MAX_RANGE_DAYS = 366;

    /**
     * @var UnitAbsenceSchedule
     */
    private UnitAbsenceSchedule $model;

    /**
     * @var Repository
     */
    private Repository $repository;

    public function __construct()
    {
        $this->model = new UnitAbsenceSchedule();
        $this->repository = new Repository($this->model);
    }

    /**
     * Toutes les entrées, filtrables via query_ comme les autres
     * ressources — ex. query_={"where_unit_id":3}
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
     * Le calendrier d'une unité sur une période — ce que le frontend
     * affiche en bandes : qui est absent, qui a demandé à l'être.
     *
     * Les entrées commencées avant la fenêtre ou finies après y
     * figurent : elles occupent bien le calendrier.
     *
     * @param Request $request
     * @param int $unitId
     */
    public function forUnit(Request $request, int $unitId)
    {
        $range = $this->resolveRange($request);

        if (isset($range['error'])) {
            return $this->respondBadRequest($range['error']);
        }

        return $this->respondOk(
            UnitAbsenceSchedule::with(['employee', 'absenceType:id,code,label'])
                ->where('unit_id', $unitId)
                ->overlapping($range['from']->toDateString(), $range['to']->toDateString())
                ->orderBy('start_date')
                ->get()
        );
    }

    /**
     * La couverture d'une unité sur une période.
     *
     * Le comptage se fait JOUR PAR JOUR, jamais sur la plage entière :
     * quelqu'un absent le 3 et quelqu'un absent le 25 ne sont pas
     * partis en même temps, et les additionner donnerait une pénurie
     * qui n'existe pas. C'est le pic quotidien qui décide.
     *
     * on_leave  : absences validées, acquises.
     * requested : demandes en attente. Le RH voit ce que deviendrait
     *             la journée s'il les approuvait toutes.
     *
     * @param Request $request
     * @param int $unitId
     */
    public function coverage(Request $request, int $unitId)
    {
        $range = $this->resolveRange($request);

        if (isset($range['error'])) {
            return $this->respondBadRequest($range['error']);
        }

        $staffing = UnitStaffing::where('unit_id', $unitId)->first();

        if (! $staffing) {
            return $this->respondBadRequest([
                'unit' => "Aucune unité active ne porte l'identifiant {$unitId}.",
            ]);
        }

        $headcount = (int) $staffing->headcount;

        $entries = UnitAbsenceSchedule::with(['employee', 'absenceType:id,code,label'])
            ->where('unit_id', $unitId)
            ->overlapping($range['from']->toDateString(), $range['to']->toDateString())
            ->orderBy('start_date')
            ->get();

        $days = [];
        $peakOnLeave = 0;
        $peakProjected = 0;

        for ($day = $range['from']->copy(); $day->lte($range['to']); $day->addDay()) {
            $covering = $entries->filter(
                fn ($e) => $e->start_date->lte($day) && $e->end_date->gte($day)
            );

            $onLeave = $covering->where('entry_status', 'confirmed')
                ->pluck('employee_id')->unique()->count();

            $requested = $covering->where('entry_status', 'pending')
                ->pluck('employee_id')->unique()->count();

            $days[] = [
                'date' => $day->toDateString(),
                'headcount' => $headcount,
                'on_leave' => $onLeave,
                'requested' => $requested,
                'present' => max(0, $headcount - $onLeave),
                'present_if_all_approved' => max(0, $headcount - $onLeave - $requested),
            ];

            $peakOnLeave = max($peakOnLeave, $onLeave);
            $peakProjected = max($peakProjected, $onLeave + $requested);
        }

        return $this->respondOk([
            'unit' => [
                'id' => $staffing->unit_id,
                'code' => $staffing->unit_code,
                'label' => $staffing->unit_label,
                'parent_id' => $staffing->parent_id,
            ],
            'from' => $range['from']->toDateString(),
            'to' => $range['to']->toDateString(),
            'headcount' => $headcount,
            'peak_on_leave' => $peakOnLeave,
            'peak_if_all_approved' => $peakProjected,
            'lowest_presence' => max(0, $headcount - $peakOnLeave),
            'days' => $days,
            'entries' => $entries,
        ]);
    }

    /**
     * L'effectif de chaque unité active. Sert au frontend à peupler
     * un sélecteur d'unité sans un appel par unité.
     *
     * @param Request $request
     */
    public function staffing(Request $request)
    {
        $query = UnitStaffing::query();

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        return $this->respondOk($query->orderBy('unit_label')->get());
    }

    /**
     * La fenêtre demandée, ou le mois en cours par défaut.
     * Renvoie un tableau contenant 'error' si elle est inexploitable.
     */
    private function resolveRange(Request $request): array
    {
        try {
            $from = Carbon::parse($request->input('from', Carbon::now()->startOfMonth()))->startOfDay();
            $to = Carbon::parse($request->input('to', Carbon::now()->endOfMonth()))->startOfDay();
        } catch (\Exception $e) {
            return ['error' => ['from' => 'Dates illisibles. Format attendu : AAAA-MM-JJ.']];
        }

        if ($to->lt($from)) {
            return ['error' => ['to' => 'La date de fin précède la date de début.']];
        }

        if ($from->diffInDays($to) > self::MAX_RANGE_DAYS) {
            return ['error' => [
                'to' => 'La période ne peut pas dépasser ' . self::MAX_RANGE_DAYS . ' jours.',
            ]];
        }

        return ['from' => $from, 'to' => $to];
    }
}

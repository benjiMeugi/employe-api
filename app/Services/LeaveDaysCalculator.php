<?php

namespace App\Services;

use App\Models\AbsenceType;
use App\Models\Holiday;
use Carbon\Carbon;

/**
 * Point d'entrée unique du calcul de durée. Appelé par AbsenceRequest
 * comme par Absence — si chacun recalculait de son côté, une demande
 * de 3 jours pourrait devenir une absence de 4.
 */
class LeaveDaysCalculator
{
    /**
     * Depuis une date de début et un nombre de jours, renvoie la date
     * de fin. Sens de calcul retenu : la durée est saisie, la date de
     * fin en découle.
     *
     * - is_calendar_based = true  : jours calendaires bruts
     * - is_calendar_based = false : on avance en sautant weekends et fériés
     */
    public function computeEndDate(string $startDate, float $daysCount, AbsenceType $type): string
    {
        $current = Carbon::parse($startDate);

        if ($daysCount <= 0) {
            return $current->toDateString();
        }

        if ($type->is_calendar_based) {
            return $current->copy()->addDays((int) ceil($daysCount) - 1)->toDateString();
        }

        $holidays = $this->holidaySet($current, $current->copy()->addDays(365));
        $counted = 0;

        // Le premier jour compte s'il est ouvré ; sinon on avance
        // jusqu'au premier jour ouvré, qui devient le jour 1.
        while (true) {
            if ($this->isWorkingDay($current, $holidays)) {
                $counted++;
                if ($counted >= $daysCount) {
                    break;
                }
            }
            $current->addDay();
        }

        return $current->toDateString();
    }

    /**
     * Le chemin inverse : combien de jours représente cet intervalle ?
     * Utile pour vérifier une saisie, ou reprendre une absence
     * enregistrée avant ce calcul.
     */
    public function countDays(string $startDate, string $endDate, AbsenceType $type): float
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($type->is_calendar_based) {
            return $start->diffInDays($end) + 1;
        }

        $holidays = $this->holidaySet($start, $end);
        $count = 0;
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            if ($this->isWorkingDay($cursor, $holidays)) {
                $count++;
            }
            $cursor->addDay();
        }

        return $count;
    }

    /**
     * Un férié tombant un samedi ne doit pas être retiré deux fois —
     * d'où un test unique plutôt que deux soustractions successives.
     */
    private function isWorkingDay(Carbon $date, array $holidays): bool
    {
        if ($date->isSaturday() || $date->isSunday()) {
            return false;
        }

        return ! in_array($date->toDateString(), $holidays, true);
    }

    /**
     * Les dates fériées de la période, sous forme de simple tableau de
     * chaînes — une seule requête plutôt qu'une par jour parcouru.
     */
    private function holidaySet(Carbon $start, Carbon $end): array
    {
        return Holiday::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->all();
    }
}

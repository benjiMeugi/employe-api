<?php

namespace App\Models;

use App\Models\Traits\BelongsToCareerEvent;
use App\Services\LeaveDaysCalculator;
use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model implements IModel
{
    /** @use HasFactory<\Database\Factories\AbsenceFactory> */
    use HasFactory;
    use BelongsToCareerEvent;

    public $incrementing = false;

    /**
     * Fillable column of the related table.
     * employee_id et event_date sont empruntés à CareerEvent — le trait
     * les retire avant l'insertion réelle dans `absences`.
     *
     * duration_days et duration_hours : une seule des deux est renseignée,
     * selon le duration_unit du type. Les permissions horaires ne
     * consomment aucun solde, les deux unités ne se croisent jamais.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'event_date',
        'absence_type_id',
        'absence_request_id',
        'start_date',
        'duration_days',
        'duration_hours',
        'end_date',
        'excess_is_deductible',
    ];

    /**
     * Trois calculs à la création :
     *
     * 1. end_date — pour les absences en jours seulement. Une permission
     *    horaire commence et finit le même jour.
     * 2. deducted_days — les jours imputés au congé annuel. Figé ici,
     *    jamais recalculé : si quota_days change plus tard dans le
     *    catalogue, ce qui a été prélevé reste ce qui a été prélevé.
     */
    protected static function bootAbsence(): void
    {
        static::creating(function (Absence $absence) {
            $type = AbsenceType::find($absence->absence_type_id);

            if (! $type) {
                return;
            }

            // Même contrôle qu'à la demande : sans lui, un RH pourrait
            // contourner la restriction en créant l'absence directement.
            $employee = Employe::find($absence->employee_id);

            if ($employee && ! $type->isOpenTo($employee)) {
                throw new \RuntimeException(
                    "Le type d'absence « {$type->label} » n'est pas ouvert à cet employé."
                );
            }

            if ($type->isHourly()) {
                $absence->end_date = $absence->start_date;
                $absence->duration_days = null;
                $absence->deducted_days = 0;
                return;
            }

            $absence->duration_hours = null;

            if ($absence->start_date && $absence->duration_days) {
                $absence->end_date = (new LeaveDaysCalculator())->computeEndDate(
                    $absence->start_date,
                    (float) $absence->duration_days,
                    $type
                );
            }

            $absence->deducted_days = self::computeDeductedDays($absence, $type);
        });
    }

    /**
     * L'excédent au-delà du quota, s'il a été déclaré déductible par
     * le RH. Le congé annuel ne prélève jamais sur lui-même : sa
     * consommation est déjà comptée directement.
     */
    private static function computeDeductedDays(Absence $absence, AbsenceType $type): float
    {
        if ($type->isAccruing()) {
            return 0;
        }

        if (! $absence->excess_is_deductible) {
            return 0;
        }

        $excess = (float) $absence->duration_days - (float) $type->quota_days;

        return max(0, $excess);
    }

    /**
     * Get the migrate key for the model.
     */
    public function getMigrateKey()
    {
        return $this->getForeignKey();
    }

    /**
     * Get the validation rules for the model.
     * (end_date et deducted_days n'apparaissent jamais ici — toujours
     * calculés côté serveur.)
     */
    public function rules()
    {
        return array_merge((new CareerEvent)->rules(), [
            'absence_type_id' => ['required', 'exists:' . (new AbsenceType)->getTable() . ',id'],
            'absence_request_id' => ['nullable', 'exists:' . (new AbsenceRequest)->getTable() . ',id'],
            'start_date' => ['required', 'date'],
            'duration_days' => ['required_without:duration_hours', 'nullable', 'integer', 'min:1'],
            'duration_hours' => ['required_without:duration_days', 'nullable', 'integer', 'min:1'],
            'excess_is_deductible' => ['required', 'boolean'],
        ]);
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'start_date' => ['sometimes', 'date'],
            'duration_days' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'duration_hours' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'excess_is_deductible' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['careerEvent', 'absenceType', 'absenceRequest', 'attachments'];

    public function careerEvent()
    {
        return $this->belongsTo(CareerEvent::class, 'id');
    }

    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function absenceRequest()
    {
        return $this->belongsTo(AbsenceRequest::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}

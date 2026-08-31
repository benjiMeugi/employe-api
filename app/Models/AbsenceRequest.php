<?php

namespace App\Models;

use App\Services\LeaveDaysCalculator;
use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceRequest extends Model implements IModel
{
    /** @use HasFactory<\Database\Factories\AbsenceRequestFactory> */
    use HasFactory;

    public static $STATUS_OPTIONS = ['pending', 'approved', 'rejected'];

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'absence_type_id',
        'requested_start_date',
        'requested_days',
        'requested_hours',
        'requested_end_date',
        'reason',
        'status',
        'approver_id',
        'decision_datetime',
        'decision_comment',
    ];

    /**
     * - La durée est saisie, la date de fin en découle (jamais l'inverse) :
     *   c'est le nombre de jours qui compte partout dans le modèle, la
     *   date de fin n'est qu'une conséquence.
     * - status est toujours forcé à "pending" à la création, même si le
     *   client l'envoie : personne ne s'auto-approuve.
     * - Aucun blocage sur solde insuffisant : la demande passe, c'est
     *   le RH qui tranche ensuite.
     */
    protected static function booted(): void
    {
        static::saving(function (AbsenceRequest $request) {
            $type = AbsenceType::find($request->absence_type_id);
            $employee = Employe::find($request->employee_id);

            if ($type && $employee && ! $type->isOpenTo($employee)) {
                throw new \RuntimeException(
                    "Le type d'absence « {$type->label} » n'est pas ouvert à cet employé."
                );
            }
        });

        static::creating(function (AbsenceRequest $request) {
            $request->status = 'pending';
            $request->approver_id = null;
            $request->decision_datetime = null;
            $request->decision_comment = null;
        });

        static::saving(function (AbsenceRequest $request) {
            if (! $request->isDirty(['requested_start_date', 'requested_days', 'requested_hours', 'absence_type_id'])) {
                return;
            }

            $type = AbsenceType::find($request->absence_type_id);

            if (! $type || ! $request->requested_start_date) {
                return;
            }

            // Une permission horaire commence et finit le même jour.
            if ($type->isHourly()) {
                $request->requested_days = null;
                $request->requested_end_date = $request->requested_start_date;
                return;
            }

            $request->requested_hours = null;

            if ($request->requested_days) {
                $request->requested_end_date = (new LeaveDaysCalculator())->computeEndDate(
                    $request->requested_start_date,
                    (float) $request->requested_days,
                    $type
                );
            }
        });
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
     * (requested_end_date et status n'apparaissent jamais ici — toujours
     * déterminés côté serveur.)
     */
    public function rules()
    {
        return [
            'employee_id' => ['required', 'exists:' . (new Employe)->getTable() . ',id'],
            'absence_type_id' => ['required', 'exists:' . (new AbsenceType)->getTable() . ',id'],
            'requested_start_date' => ['required', 'date'],
            'requested_days' => ['required_without:requested_hours', 'nullable', 'integer', 'min:1'],
            'requested_hours' => ['required_without:requested_days', 'nullable', 'integer', 'min:1'],
            'reason' => ['required', 'max:255'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'requested_start_date' => ['sometimes', 'date'],
            'requested_days' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'requested_hours' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'reason' => ['sometimes'],
            'status' => ['sometimes', 'in:' . implode(',', self::$STATUS_OPTIONS)],
            'approver_id' => ['sometimes', 'nullable', 'exists:' . (new Employe)->getTable() . ',id'],
            'decision_datetime' => ['sometimes', 'nullable', 'date'],
            'decision_comment' => ['sometimes', 'nullable', 'max:255'],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['employee', 'absenceType', 'approver', 'attachments'];

    public function employee()
    {
        return $this->belongsTo(Employe::class, 'employee_id');
    }

    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employe::class, 'approver_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}

<?php

namespace App\Models;

use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveGrant extends Model implements IModel
{
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory;

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'absence_type_id',
        'period',
        'granted_days',
        'grant_date',
    ];

    /**
     * Get the migrate key for the model.
     */
    public function getMigrateKey()
    {
        return $this->getForeignKey();
    }


    /**
     * Get the validation rules for the model.
     */
    public function rules()
    {
        return [
            'employee_id' => ['required', 'exists:' . (new Employe)->getTable() . ',id'],
            'absence_type_id' => ['required', 'exists:' . (new AbsenceType)->getTable() . ',id'],
            'period' => ['required', 'max:255'],
            'granted_days' => ['required', 'numeric'],
            'grant_date' => ['required', "date"],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'employee_id' => ['sometimes', 'exists:' . (new Employe)->getTable() . ',id'],
            'absence_type_id' => ['sometimes', 'exists:' . (new AbsenceType)->getTable() . ',id'],
            'period' => ['sometimes', 'max:255'],
            'granted_days' => ['sometimes', 'numeric'],
            'grant_date' => ['sometimes', "date"],
        ];
    }

    /**
     * expiration_date est toujours déduite du catalogue, jamais saisie —
     * et figée à la création : si expiration_delay_months change plus
     * tard, les octrois déjà émis gardent leur échéance d'origine.
     */
    protected static function booted(): void
    {
        static::creating(function (LeaveGrant $grant) {
            if ($grant->expiration_date) {
                return;
            }

            $type = AbsenceType::find($grant->absence_type_id);

            if (! $type || ! $type->expiration_delay_months) {
                return;
            }

            $grantDate = \Carbon\Carbon::parse($grant->grant_date);

            if ($type->expiration_mode === 'per_year') {
                // Tous les octrois de l'année d'acquisition en cours
                // expirent ensemble : fin de cette année + le délai.
                $contract = Contract::where('employee_id', $grant->employee_id)
                    ->where('status', 'Active')
                    ->latest('start_date')
                    ->first();

                $anchor = $contract
                    ? \Carbon\Carbon::parse($contract->start_date)
                    : $grantDate;

                // Fin de l'année d'acquisition en cours, à partir de l'ancrage.
                $yearEnd = $anchor->copy();
                while ($yearEnd->lte($grantDate)) {
                    $yearEnd->addYearNoOverflow();
                }

                $grant->expiration_date = $yearEnd
                    ->addMonthsNoOverflow($type->expiration_delay_months)
                    ->toDateString();
            } else {
                $grant->expiration_date = $grantDate
                    ->copy()
                    ->addMonthsNoOverflow($type->expiration_delay_months)
                    ->toDateString();
            }
        });
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ["employee", "absenceType" ];

    public function employee()
    {
        return $this->belongsTo(Employe::class);
    }

    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class);
    }

}

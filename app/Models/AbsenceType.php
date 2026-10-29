<?php

namespace App\Models;

use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceType extends Model implements IModel
{
    /** @use HasFactory<\Database\Factories\AbsenceTypeFactory> */
    use HasFactory;

    /**
     * per_period     : le quota vaut pour toute la période (ex. 3 j/an)
     * per_occurrence : le quota se rouvre à chaque événement (ex. 3 j par décès)
     */
    public static $QUOTA_BASIS_OPTIONS = ['per_period', 'per_occurrence'];

    /**
     * per_grant : chaque octroi expire expiration_delay_months après sa date
     * per_year  : tous les octrois d'une année d'acquisition expirent ensemble
     */
    public static $EXPIRATION_MODE_OPTIONS = ['per_grant', 'per_year'];

    /**
     * day  : la durée se saisit en jours (congés, permissions journalières)
     * hour : la durée se saisit en heures (permissions de quelques heures)
     *
     * Les types en heures ne consomment jamais de solde — les deux
     * unités ne se croisent donc jamais dans un calcul.
     */
    public static $DURATION_UNIT_OPTIONS = ['day', 'hour'];

    /**
     * none : ouvert à tous
     * M / F : réservé à un sexe (paternité, maternité)
     */
    public static $GENDER_RESTRICTION_OPTIONS = ['none', 'M', 'F'];

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'label',
        'is_paid',
        'is_calendar_based',
        'duration_unit',
        'gender_restriction',
        'quota_days',
        'quota_basis',
        'accrual_rate_per_month',
        'expiration_delay_months',
        'expiration_mode',
        'requires_supporting_document',
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
            'code' => ['required', 'unique:' . $this->getTable(), 'max:255'],
            'label' => ['required', 'max:255'],
            'is_paid' => ['required', 'boolean'],
            'is_calendar_based' => ['required', 'boolean'],
            'duration_unit' => ['required', 'in:' . implode(',', self::$DURATION_UNIT_OPTIONS)],
            'gender_restriction' => ['required', 'in:' . implode(',', self::$GENDER_RESTRICTION_OPTIONS)],
            'quota_days' => ['required', 'numeric', 'min:0'],
            'quota_basis' => ['required', 'in:' . implode(',', self::$QUOTA_BASIS_OPTIONS)],
            'accrual_rate_per_month' => ['required', 'numeric', 'min:0'],
            'expiration_delay_months' => ['nullable', 'integer', 'min:0'],
            'expiration_mode' => ['nullable', 'in:' . implode(',', self::$EXPIRATION_MODE_OPTIONS)],
            'requires_supporting_document' => ['required', 'boolean'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'code' => ['sometimes', 'unique:' . $this->getTable() . ',code,' . $this->id],
            'label' => ['sometimes'],
            'is_paid' => ['sometimes', 'boolean'],
            'is_calendar_based' => ['sometimes', 'boolean'],
            'duration_unit' => ['sometimes', 'in:' . implode(',', self::$DURATION_UNIT_OPTIONS)],
            'gender_restriction' => ['sometimes', 'in:' . implode(',', self::$GENDER_RESTRICTION_OPTIONS)],
            'quota_days' => ['sometimes', 'numeric', 'min:0'],
            'quota_basis' => ['sometimes', 'in:' . implode(',', self::$QUOTA_BASIS_OPTIONS)],
            'accrual_rate_per_month' => ['sometimes', 'numeric', 'min:0'],
            'expiration_delay_months' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'expiration_mode' => ['sometimes', 'nullable', 'in:' . implode(',', self::$EXPIRATION_MODE_OPTIONS)],
            'requires_supporting_document' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Ce type s'accumule-t-il mensuellement ? (congé annuel uniquement,
     * en pratique) — déduit du taux, jamais stocké séparément.
     */
    public function isAccruing(): bool
    {
        return $this->accrual_rate_per_month > 0;
    }

    /**
     * Les permissions horaires ne touchent jamais aucun solde.
     */
    public function isHourly(): bool
    {
        return $this->duration_unit === 'hour';
    }

    /**
     * Ce type est-il ouvert à cet employé ? La restriction de sexe
     * doit être vérifiée à la demande ET à l'absence — sinon un RH
     * pourrait la contourner en créant l'absence directement.
     */
    public function isOpenTo(Employe $employee): bool
    {
        return $this->gender_restriction === 'none'
            || $this->gender_restriction === $employee->gender;
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['leaveGrants', 'absences'];

    public function leaveGrants()
    {
        return $this->hasMany(LeaveGrant::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\BelongsToCareerEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory;

    /**
     * Utilisation d'un trait permettant de regrouper la logique de creation
     * du career_event
     */
    use BelongsToCareerEvent;

    /**
     * Son id vient toujours de career_events, jamais auto-généré ici.
     *
     * @var bool
     */
    public $incrementing = false;

    public static $STATUS_OPTIONS = ['pending','approved', 'rejected'];

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'event_date',
        'absence_type_id',
        'start_date',
        'end_date',
        'days_count',
        'absence_request_id',
        'leave_credit_id',
        "is_deductible",
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
            'absence_type_id' => ['required', 'exists:' . (new AbsenceType)->getTable() . ',id'],
            'days_count' => ['required', 'numeric'],
            'start_date' => ['required', "date"],
            'absence_request_id' => ['nullable', 'exists:' . ((new AbsenceRequest)->getTable() . ',id')],
            'leave_credit_id' => ['nullable', 'exists:' . (new LeaveCredit)->getTable() . ',id'],
            'is_deductible' => ['nullable', 'boolean']
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'absence_type_id' => ['sometimes'],
            'leave_credit_id' => ['sometimes',],
            'days_count' => ['sometimes',],
            'start_date' => ['sometimes', 'date'],
            'absence_request_id' => ['sometimes'],
            'is_deductible' => ['sometimes', 'boolean']
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ["careerEvent", "absenceType", "attachments" ];

    public function careerEvent()
    {
        return $this->belongsTo(CareerEvent::class, 'id');
    }

    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

}

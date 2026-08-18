<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceRequest extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory;

    public static $STATUS_OPTIONS = ['pending','approved', 'rejected'];

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'absence_type_id',
        'requested_start_date',
        'requested_end_date',
        'requested_days_count',
        'reason',
        'status',
        'approver_id',
        "decision_datetime",
        "decision_comment",
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
            'reason' => ['required', 'max:255'],
            'requested_days_count' => ['required', 'numeric'],
            'requested_start_date' => ['required', "date"],
            'requested_end_date' => ['required', 'date'],
            'approver_id' => ['nullable', 'exists:' . ((new Employe)->getTable() . ',id')],
            'decision_datetime' => ['nullable', "date"],
            'decision_comment' => ['nullable', 'max:255'],

        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'employee_id' => ['sometimes'],
            'absence_type_id' => ['sometimes'],
            'reason' => ['sometimes',],
            'requested_days_count' => ['sometimes',],
            'requested_start_date' => ['sometimes', 'date'],
            'requested_end_date' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:' . implode(',', self::$STATUS_OPTIONS)],
            'approver_id' => ['sometimes'],
            'decision_datetime' => ['sometimes', 'date'],
            'decision_comment' => ['sometimes', 'max:255'],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ["employee", "absenceType", "attachments" ];

    public function employee()
    {
        return $this->belongsTo(Employe::class);
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

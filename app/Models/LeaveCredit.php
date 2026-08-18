<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveCredit extends Model
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
        'acquired_days',
        'acquisition_date',
        'expiration_date',
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
            'acquired_days' => ['required', 'numeric'],
            'acquisition_date' => ['required', "date"],
            'expiration_date' => ['required', 'date'],
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
            'period' => ['sometimes',],
            'acquired_days' => ['sometimes',],
            'acquisition_date' => ['sometimes', 'date'],
            'expiration_date' => ['sometimes', 'date'],
        ];
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceType extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory;

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'label',
        'is_paid',
        'is_cumulative',
        'max_cumulative_years',
        'day_cap',
        'expiration_delay_months',
        'requires_supporting_documents'
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
            'is_cumulative' => ['required', 'boolean'],
            'max_cumulative_years' => ['required', "integer"],
            'days_cap' => ['required', 'numeric'],
            'expiration_delay_month' => ['required', 'integer'],
            'requires_supporting_documents' => ['required', 'boolean'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'code' => ['sometimes'],
            'label' => ['sometimes'],
            'is_paid' => ['sometimes', 'boolean'],
            'is_cumulative' => ['sometimes', 'boolean'],
            'max_cumulative_years' => ['sometimes'],
            'days_cap' => ['sometimes'],
            'expiration_delay_month' => ['sometimes'],
            'requires_supporting_documents' => ['sometimes', 'boolean']
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = [];

}

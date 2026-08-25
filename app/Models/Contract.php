<?php

namespace App\Models;

use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model 
{
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory;

    public static $STATUS_OPTIONS = ['Active', 'Terminated', 'Suspended'];

        /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'contract_type_id',
        'start_date',
        'end_date',
        'pay_frequency',
        'base_salary',
        'status',
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
            'pay_frequency' => ['required', 'max:255'],
            'base_salary' => ['required', 'numeric'],
            'status' => ['required', 'in:' . implode(',', self::$STATUS_OPTIONS)],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'employee_id' => ['required', 'exists:' . (new Employe)->getTable() . ',id'],
            'contract_type_id' => ['required', 'exists:' . (new ContractType)->getTable() . ',id']
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
           
            'pay_frequency' => ['sometimes', 'string', 'max:255'],
            'base_salary' => ['sometimes', 'numeric'],
            'status' => ['sometimes', 'in:' . implode(',', self::$STATUS_OPTIONS)],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date'],
            'employee_id' => ['sometimes', 'exists:' . (new Employe)->getTable() . ',id'],
            'contract_type_id' => ['sometimes', 'exists:' . (new ContractType)->getTable() . ',id']
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['contractType', 'employe'];

    public function contractType() 
    { 
        return $this->belongsTo(contractType::class);
    }

    public function employe(): BelongsTo
    { 
        return $this->belongsTo(employe::class, 'employee_id');
    }


}


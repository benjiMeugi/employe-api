<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractType extends Model
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
        'is_fixed_term',
        'max_duration_months'
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
            'label' => ['required', 'max:255'],
            'max_duration_months' => ['required', 'numeric'],
            'is_fixed_term' => ['required', 'boolean'],
            'code' => ['required', 'unique:' . $this->getTable(), 'max:255'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'label' => ['sometimes'],
            'max_duration_months' => ['sometimes'],
            'is_fixed_term' => ['sometimes'],
            'code' => ['sometimes'],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['contracts'];

    public function contracts() 
    { 
        return $this->hasMany(Contract::class); 
    }

}

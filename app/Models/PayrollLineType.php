<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLineType extends Model 
{
    protected $fillable = ['code', 'label', 'nature', 'calculation_mode', 'is_taxable', 'is_subject_to_contributions', 'is_employer_contribution'];
    

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
            'label' => ['required', 'unique:' . $this->getTable(), 'max:255'],
            'nature' => ['required'],
            'calculation_mode' => ['required'],
            'is_taxable' => ['required','boolean'],
            'is_subject_to_contributions' => ['required','boolean'],
            'is_employer_contribution' => ['required','boolean']
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'code'=> ['sometimes', 'required', IModel::IGNORE_RULE],
            'label'=> ['sometimes', 'required', IModel::IGNORE_RULE],
            'nature'=> ['sometimes'],
            'calculation_mode'=> ['sometimes'],
            'is_taxable'=> ['sometimes'],
            'is_subject_to_contributions'=> ['sometimes'],
            'is_employer_contribution'=> ['sometimes']
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    
    }

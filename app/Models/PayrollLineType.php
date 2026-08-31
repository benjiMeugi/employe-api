<?php

namespace App\Models;

use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Model;



class PayrollLineType extends Model implements IModel
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
            'nature'=> ['sometimes', 'in:Earning,Deduction'],
            'calculation_mode'=> ['sometimes', 'in:Rate,FixedAmount,Formula'],
            'is_taxable'=> ['sometimes', 'boolean'],
            'is_subject_to_contributions'=> ['sometimes', 'boolean'],
            'is_employer_contribution'=> ['sometimes', 'boolean']
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['classifications', 'payslipLines'];

    public function classifications()
    {
        return $this->belongsToMany(
            Classification::class,
            'classification_payroll_line_type',
            'payroll_line_type_id',
            'classification_id'
        )->withPivot('value')->withTimestamps();
    }

    public function payslipLines()
    {
        return $this->hasMany(PayslipLine::class);
    }
}

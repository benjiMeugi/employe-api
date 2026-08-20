<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayslipLine extends Model 
{
    protected $fillable = ['payslip_id', 'payroll_line_type_id', 'calculation_base', 'rate', 'amount'];

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
            'payslip_id' => ['required', 'exists:' . (new payslip)->getTable() . ',id'],
            'payroll_line_type_id' => ['required', 'exists:' . (new payslip)->getTable() . ',id'],
            'calculation_base' => ['required'],
            'rate' => ['required'],
            'amount' => ['required'],
            
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'payslip_id' => ['sometimes'],
            'payroll_line_type_id' => ['sometimes',],
            'calculation_base' => ['sometimes'],
            'rate' => ['sometimes'],
             'amount' => ['sometimes'],
            
        ];
    }


      /**
     * Get the relation methods for the model.
     */
    public $relation_methods = [' payslip', 'payrollLineType',];

    public function payslip(): BelongsTo 
    { 
        return $this->belongsTo(Payslip::class); 
    }

    public function payrollLineType(): BelongsTo 
    { 
        return $this->belongsTo(PayrollLineType::class); 
    }
}

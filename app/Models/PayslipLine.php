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
            'payroll_line_type_id' => ['required', 'exists:' . (new PayrollLineType)->getTable() . ',id'],
            'calculation_base' => ['nullable', 'numeric'],
            'rate' => ['nullable', 'numeric'],
            'amount' => ['required'],
            
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'payslip_id' => ['sometimes', 'exists:' . (new Payslip)->getTable() . ',id'],
            'payroll_line_type_id' => ['sometimes', 'exists:' . (new PayrollLineType)->getTable() . ',id'],
            'calculation_base' => ['sometimes', 'nullable', 'numeric'],
            'rate' => ['sometimes', 'nullable', 'numeric'],
            'amount' => ['sometimes', 'numeric'],
            
        ];
    }


      /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['payslip', 'payrollLineType'];

    public function payslip(): BelongsTo 
    { 
        return $this->belongsTo(payslip::class); 
    }

    public function payrollLineType(): BelongsTo 
    { 
        return $this->belongsTo(payrollLineType::class); 
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayslipLine extends Model 
{
    protected $fillable = ['payslip_id', 'payroll_line_type_id', 'calculation_base', 'rate', 'amount'];

    public function payslip(): BelongsTo 
    { 
        return $this->belongsTo(Payslip::class); 
    }

    public function payrollLineType(): BelongsTo 
    { 
        return $this->belongsTo(PayrollLineType::class); 
    }
}

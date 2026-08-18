<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payslip extends Model 
{
    protected $fillable = ['employee_id', 'contract_id', 'period', 'issue_date', 'gross_salary', 'total_earnings', 'total_deductions', 'net_pay', 'status'];

    public function contract(): BelongsTo 
    { 
        return $this->belongsTo(Contract::class); 
    }

    public function lines(): HasMany 
    { 
        return $this->hasMany(PayslipLine::class); 
    }
}

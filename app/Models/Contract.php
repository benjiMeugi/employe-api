<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model 
{
    protected $fillable = ['employee_id', 'contract_type_id', 'start_date', 'end_date', 'pay_frequency', 'base_salary', 'status'];

    public function contractType(): BelongsTo 
    { 
        return $this->belongsTo(ContractType::class); 
    }

    public function payslips(): HasMany 
    { 
        return $this->hasMany(Payslip::class); 
    }
}

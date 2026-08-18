<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model 
{
    protected $fillable = ['employee_id', 'absence_type_id', 'year', 'cumulative_acquired_days', 'consumed_days', 'expired_days', 'available_balance'];

    public function absenceType(): BelongsTo 
    { 
        return $this->belongsTo(AbsenceType::class); 
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveCredit extends Model 
{
    protected $fillable = ['employee_id', 'absence_type_id', 'period', 'acquired_days', 'acquisition_date', 'expiration_date'];

    public function absenceType(): BelongsTo 
    { 
        return $this->belongsTo(AbsenceType::class); 
    }
}

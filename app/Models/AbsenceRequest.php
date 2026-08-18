<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AbsenceRequest extends Model 
{
    protected $fillable = ['employee_id', 'absence_type_id', 'requested_start_date', 'requested_end_date', 'requested_days_count', 'reason', 'status', 'approver_id', 'decision_datetime', 'decision_comment', 'is_deductible'];

    public function absenceType(): BelongsTo 
    { 
        return $this->belongsTo(AbsenceType::class); 
    }

    public function absences(): HasMany 
    { 
        return $this->hasMany(Absence::class); 
    }
}

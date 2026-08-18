<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    protected $fillable = ['absence_type_id', 'absence_request_id', 'start_date', 'end_date', 'days_count', 'is_deductible'];

    public function absenceType(): BelongsTo 
    { 
        return $this->belongsTo(AbsenceType::class); 
    }

    public function absenceRequest(): BelongsTo 
    { 
        return $this->belongsTo(AbsenceRequest::class); 
    }
}

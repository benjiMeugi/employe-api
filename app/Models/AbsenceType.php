<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AbsenceType extends Model 
{
    protected $fillable = ['code', 'label', 'is_paid', 'is_cumulative', 'max_cumulative_years', 'day_cap', 'expiration_delay_months', 'requires_supporting_document'];

    public function absenceRequests(): HasMany 
    { 
        return $this->hasMany(AbsenceRequest::class); 
    }

    public function absences(): HasMany 
    { 
        return $this->hasMany(Absence::class); 
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractType extends Model
{
    protected $fillable = ['code', 'label', 'is_fixed_term', 'max_duration_months'];

    public function contracts(): HasMany 
    { 
        return $this->hasMany(Contract::class); 
    }
}

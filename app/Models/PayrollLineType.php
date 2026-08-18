<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLineType extends Model 
{
    protected $fillable = ['code', 'label', 'nature', 'calculation_mode', 'is_taxable', 'is_subject_to_contributions', 'is_employer_contribution'];
}

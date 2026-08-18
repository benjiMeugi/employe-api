<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassificationPayrollLineType extends Pivot 
{
    // Indique à Laravel le nom exact de la table pivot en minuscules
    protected $table = 'classification_payroll_line_type';

    protected $fillable = [
        'classification_id', 
        'payroll_line_type_id', 
        'value'
    ];

    // Désactive l'incrémentation automatique car la clé est composite
    public $incrementing = false;
}

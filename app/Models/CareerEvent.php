<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerEvent extends Model
{
    /** @use HasFactory<\Database\Factories\CareerEventFactory> */
    use HasFactory;

    public static $EVENT_OPTIONS = ['assignment', 'promotion', 'sanction', 'retirement', 'dismissal'];

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'event_date',
        'user_id',
        'event',
    ];

    /**
     * Get the migrate key for the model.
     */
    public function getMigrateKey()
    {
        return $this->getForeignKey();
    }

    /**
     * Get the validation rules for the model.
     */
    public function rules()
    {
        return [
            'employee_id' => ['required', 'exists:' . (new Employe)->getTable() . ',id'],
            'event_date' => ['sometimes', 'date'],
            'user_id' => ['nullable'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'event_date' => ['sometimes', 'date'],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['employe'];

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employee_id');
    }
}

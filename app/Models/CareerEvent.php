<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerEvent extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory;

    public static $EVENT_OPTIONS = ['assignment','promotion', 'sanction', 'retirement', 'dismissal'];

        /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'employe_id',
        'event_date',
        'user_id',
        'comment',
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
            'event' => ['required', 'in:' . implode(',', self::$EVENT_OPTIONS)],
            'event_date' => ['required', 'date'],
            'comment' => ['nullable',],
            'user_id' => ['nullable'],
            'employe_id' => ['required', 'exists:' . (new Employe)->getTable() . ',id']
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'comment' => ['sometimes'],
            'event_date' => ['sometimes', 'date'],
            'event' => ['sometimes'],
            'employe_id' => ['sometimes'],
            'user_id' => ['sometimes']
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['employe', 'retirement'];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
    
    public function retirement()
    {
        return $this->hasOne(Retirement::class);
    }

}

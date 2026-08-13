<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    /** @use HasFactory<\Database\Factories\TitleFactory> */
    use HasFactory;

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'reason',
        'position_id',
        //'unit_id',
        'start_date',
        'end_date',
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
            'position_id' => ['required', 'exists:' . (new Position)->getTable() . ',id'],
            //'unit_id' => ['required', 'exists:' . (new Unit)->getTable() . 'id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'reason' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'reason' => ['sometimes'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date'],
            'position_id' => ['sometimes', 'exists:' . (new Position)->getTable() . ',id'],
            //'unit_id' => ['sometimes', 'exists:' . (new Unit)->getTable() . ',id'],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['career_event'];

    public function career_event()
    {
        return $this->belongsTo(CareerEvent::class, 'id');
    }
}

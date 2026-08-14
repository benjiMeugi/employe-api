<?php

namespace App\Models;

use App\Models\Traits\BelongsToCareerEvent;
use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model implements Imodel
{
    /** @use HasFactory<\Database\Factories\TitleFactory> */
    use HasFactory;

    /**
     * Utilisation d'un trait permettant de regrouper la logique de creation
     * du career_event
     */
    use BelongsToCareerEvent;

    /**
     * Son id vient toujours de career_events, jamais auto-généré ici.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'reason',
        'position_id',
        'unit_id',
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
            'unit_id' => ['required', 'exists:' . (new Unit)->getTable() . 'id'],
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
            'unit_id' => ['sometimes', 'exists:' . (new Unit)->getTable() . ',id'],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['careerEvent', 'position', 'unit'];

    public function careerEvent()
    {
        return $this->belongsTo(CareerEvent::class, 'id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

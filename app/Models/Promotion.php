<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    /** @use HasFactory<\Database\Factories\PromotionFactory> */
    use HasFactory;


    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'previous_position_id',
        'new_position_id',
        'previous_classification_id',
        'new_classification_id',
        'reason',
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
     * (les champs communs à CareerEvent sont validés séparément,
     * via (new CareerEvent)->rules(), fusionnés par le repository)
     */
    public function rules()
    {
        return [
            'previous_position_id' => ['nullable', 'exists:' . (new Position)->getTable() . ',id'],
            'new_position_id' => ['nullable', 'exists:' . (new Position)->getTable() . ',id'],
            'previous_classification_id' => ['nullable', 'exists:' . (new Classification)->getTable() . ',id'],
            'new_classification_id' => ['nullable', 'exists:' . (new Classification)->getTable() . ',id'],
            'reason' => ['nullable', 'max:255'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'previous_position_id' => ['sometimes', 'nullable', 'exists:' . (new Position)->getTable() . ',id'],
            'new_position_id' => ['sometimes', 'nullable', 'exists:' . (new Position)->getTable() . ',id'],
            'previous_classification_id' => ['sometimes', 'nullable', 'exists:' . (new Classification)->getTable() . ',id'],
            'new_classification_id' => ['sometimes', 'nullable', 'exists:' . (new Classification)->getTable() . ',id'],
            'reason' => ['sometimes'],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['careerEvent', 'previousPosition', 'newPosition', 'previousClassification', 'newClassification'];

    public function careerEvent()
    {
        return $this->belongsTo(CareerEvent::class, 'id');
    }

    public function previousPosition()
    {
        return $this->belongsTo(Position::class, 'previous_position_id');
    }

    public function newPosition()
    {
        return $this->belongsTo(Position::class, 'new_position_id');
    }

    public function previousClassification()
    {
        return $this->belongsTo(Classification::class, 'previous_classification_id');
    }

    public function newClassification()
    {
        return $this->belongsTo(Classification::class, 'new_classification_id');
    }
}

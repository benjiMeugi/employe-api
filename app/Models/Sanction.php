<?php

namespace App\Models;

use App\Models\Traits\BelongsToCareerEvent;
use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model implements Imodel
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

    public static $SANCTION_TYPE_OPTIONS = ['Warning', 'Suspension', 'Demotion'];

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'reason',
        'duration_days',
        'sanction_type',
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
            'reason' => ['required', 'max:255'],
            'sanction_type' => ['required', 'in:' . implode(',', self::$SANCTION_TYPE_OPTIONS)],
            'duration_days' => ['nullable', 'numeric:'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'reason' => ['sometimes'],
            'sanction_type' => ['sometimes'],
            'duration_days' => ['sometimes', 'numeric'],
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

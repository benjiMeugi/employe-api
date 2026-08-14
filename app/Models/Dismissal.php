<?php

namespace App\Models;

use App\Models\Traits\BelongsToCareerEvent;
use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dismissal extends Model implements Imodel
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
        'effective_date',
        'severance_pay',
        'notice_days',
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
            'effective_date' => ['required', 'date'],
            'reason' => ['string', 'max:255'],
            'severance_pay' => ['nullable', 'double'],
            'notice_days' => ['nullable', 'numeric:'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'effective_date' => ['sometimes', 'date'],
            'reason' => ['sometimes'],
            'severance_pay' => ['sometimes', 'double'],
            'notice_days' => ['sometimes', 'numeric'],
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

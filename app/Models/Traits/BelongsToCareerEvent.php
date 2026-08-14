<?php

namespace App\Models\Traits;

use App\Models\CareerEvent;
use Illuminate\Support\Str;

trait BelongsToCareerEvent
{

    /**
     * Laravel appelle automatiquement boot{NomDuTrait}() pour chaque
     * trait utilisé par un modèle — pas besoin de toucher à boot()
     * du modèle lui-même.
     */
    protected static function bootBelongsToCareerEvent(): void
    {
        static::creating(function ($model) {
            $careerEvent = CareerEvent::create([
                'employee_id' => $model->employee_id,
                'event_date' => $model->event_date ?? now()->toDateString(),
                'user_id' => auth()->id(),
                'event' => Str::snake(class_basename($model)),
            ]);

            $model->id = $careerEvent->id;

            // Ces champs appartiennent à career_events, jamais insérés
            // dans la table fille elle-même.
            unset($model->employee_id, $model->event_date, $model->comment);
        });

        static::deleted(function ($model) {
            CareerEvent::find($model->id)?->delete();
        });
    }

    public function careerEvent()
    {
        return $this->belongsTo(CareerEvent::class, 'id');
    }
}

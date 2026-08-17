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
     *
     * IMPORTANT : chaque modèle utilisant ce trait doit lui-même
     * déclarer `public $incrementing = false;` — impossible de le
     * faire ici, dans le trait (conflit PHP avec la propriété déjà
     * définie sur Model).
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

        static::updating(function ($model) {
            $careerEventFields = array_intersect_key(
                $model->getDirty(),
                array_flip(['event_date'])
            );

            if (! empty($careerEventFields)) {
                CareerEvent::where('id', $model->id)->update($careerEventFields);
            }

            // Retire ces champs avant l'UPDATE réel sur la table fille,
            // sinon Eloquent tenterait d'écrire dans des colonnes qui
            // n'existent pas ici (elles vivent sur career_events).
            foreach (array_keys($careerEventFields) as $field) {
                unset($model->$field);
            }
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

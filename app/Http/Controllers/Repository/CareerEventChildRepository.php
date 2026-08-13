<?php

namespace App\Http\Controllers\Repository;

use App\Models\CareerEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CareerEventChildRepository extends Repository
{
    /**
     * Store resource
     *
     * @param Request $request
     */
    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate(
                array_merge((new CareerEvent)->rules(), $this->model->rules())
            );

            // Déduit automatiquement le type depuis le nom de la classe du modèle
            // (Retirement -> 'retirement', Assignment -> 'assignment'...)
            $type = Str::snake(class_basename($this->model));

            $careerEvent = CareerEvent::create([
                'employee_id' => $validated['employee_id'],
                'event_date' => $validated['event_date'],
                'user_id' => null,//auth()->id(),
                'comment' => $validated['comment'] ?? null,
                'type' => $type,
            ]);

            $childClass = get_class($this->model);
            $child = new $childClass($validated);
            $child->id = $careerEvent->id;
            $child->exists = false;
            $child->save();

            return $child;
        });
    }
    /**
     * Delete ressource
     * @param Request $request
     * @param int|array $id
     */
    public function delete($request,$id)
    {
        CareerEvent::findOrFail($id)->delete();
    }
}

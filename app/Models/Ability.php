<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Ability extends Model
{
    /** @use HasFactory<\Database\Factories\AbilityFactory> */
    use HasFactory;

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'label',
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
            'label' => ['required', 'unique:' . $this->getTable(), 'max:255'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'label' => ['sometimes', 'unique:' . $this->getTable() . ',label,' . $this->id],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['roles'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Vide le cache Gate::before() pour cette habileté — appelé
     * explicitement par le contrôleur après un attach/detach/sync,
     * puisque ces opérations sur une relation belongsToMany ne
     * déclenchent pas les événements de modèle habituels (saved/deleted).
     */
    public function forgetCache(): void
    {
        Cache::forget("ability:{$this->label}");
    }

    protected static function booted(): void
    {
        static::deleted(fn ($model) => $model->forgetCache());
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitStaffing extends Model
{
    protected $table = 'unit_staffing';

    /**
     * Une vue n'a pas de clé primaire : ici c'est unit_id qui
     * identifie une ligne, une par unité active.
     */
    public $incrementing = false;
    protected $primaryKey = 'unit_id';
    public $timestamps = false;

    /**
     * Rien n'est jamais assignable : cette vue ne s'écrit pas.
     *
     * @var array
     */
    protected $fillable = [];

    protected $casts = [
        'headcount' => 'integer',
    ];

    /**
     * Blocage explicite plutôt qu'une erreur MySQL obscure
     * ("table is a view") si quelqu'un tente d'écrire.
     */
    public function save(array $options = [])
    {
        throw new \RuntimeException('UnitStaffing est une vue calculée, en lecture seule.');
    }

    public function delete()
    {
        throw new \RuntimeException('UnitStaffing est une vue calculée, en lecture seule.');
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['unit', 'parent'];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function parent()
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }
}

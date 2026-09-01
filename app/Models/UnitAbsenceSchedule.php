<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UnitAbsenceSchedule extends Model
{
    protected $table = 'unit_absence_schedule';

    /**
     * Une vue n'a pas de clé primaire, mais celle-ci expose un
     * entry_key stable ('absence-13', 'request-4') : il identifie une
     * ligne à travers les deux branches de l'union, et sert de clé de
     * rendu côté frontend.
     */
    public $incrementing = false;
    protected $primaryKey = 'entry_key';
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * Rien n'est jamais assignable : cette vue ne s'écrit pas.
     *
     * @var array
     */
    protected $fillable = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_days' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    /**
     * Blocage explicite plutôt qu'une erreur MySQL obscure
     * ("table is a view") si quelqu'un tente d'écrire.
     */
    public function save(array $options = [])
    {
        throw new \RuntimeException('UnitAbsenceSchedule est une vue calculée, en lecture seule.');
    }

    public function delete()
    {
        throw new \RuntimeException('UnitAbsenceSchedule est une vue calculée, en lecture seule.');
    }

    /**
     * Les entrées qui recouvrent la période, même partiellement :
     * une absence commencée avant :from et finie après :to occupe
     * bien le calendrier, il faut la voir.
     */
    public function scopeOverlapping(Builder $query, string $from, string $to): Builder
    {
        return $query->where('start_date', '<=', $to)
            ->where('end_date', '>=', $from);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('entry_status', 'confirmed');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('entry_status', 'pending');
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['employee', 'absenceType', 'unit', 'absenceRequest'];

    public function employee()
    {
        return $this->belongsTo(Employe::class, 'employee_id');
    }

    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function absenceRequest()
    {
        return $this->belongsTo(AbsenceRequest::class, 'request_id');
    }
}

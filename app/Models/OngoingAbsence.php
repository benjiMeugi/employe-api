<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OngoingAbsence extends Model
{
    protected $table = 'ongoing_absences';

    /**
     * Une ligne par absence en cours — absence_id l'identifie, mais
     * ce n'est pas une clé primaire au sens Eloquent.
     */
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    /**
     * Rien n'est jamais assignable : cette vue ne s'écrit pas.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Blocage explicite plutôt qu'une erreur MySQL obscure
     * ("table is a view") si quelqu'un tente d'écrire.
     */
    public function save(array $options = [])
    {
        throw new \RuntimeException('OngoingAbsence est une vue calculée, en lecture seule.');
    }

    public function delete()
    {
        throw new \RuntimeException('OngoingAbsence est une vue calculée, en lecture seule.');
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['employee', 'absenceType', 'absence'];

    public function employee()
    {
        return $this->belongsTo(Employe::class, 'employee_id');
    }

    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function absence()
    {
        return $this->belongsTo(Absence::class, 'absence_id');
    }
}

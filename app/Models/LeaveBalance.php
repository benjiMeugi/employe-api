<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $table = 'leave_balances';

    /**
     * Une vue n'a pas de clé primaire : c'est le couple
     * (employee_id, absence_type_id) qui identifie une ligne.
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
        throw new \RuntimeException('LeaveBalance est une vue calculée, en lecture seule.');
    }

    public function delete()
    {
        throw new \RuntimeException('LeaveBalance est une vue calculée, en lecture seule.');
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['employee', 'absenceType'];

    public function employee()
    {
        return $this->belongsTo(Employe::class, 'employee_id');
    }

    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class);
    }
}

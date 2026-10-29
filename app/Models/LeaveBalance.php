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
     * Sans ces casts, MySQL renvoie les décimaux en texte et le JSON
     * sort "30.00" au lieu de 30 : le frontend concatène là où il
     * devrait additionner.
     *
     * float et non decimal:2 — le cast decimal de Laravel produit
     * lui aussi une chaîne, ce qui ne réglerait rien.
     *
     * @var array
     */
    protected $casts = [
        'employee_id' => 'integer',
        'absence_type_id' => 'integer',
        'quota_days' => 'float',
        'occurrence_count' => 'integer',

        // Le trio qui s'additionne : acquired − consumed = balance
        'acquired_days' => 'float',
        'consumed_days' => 'float',
        'balance' => 'float',

        // Le détail de la période, hors soustraction
        'consumed_this_year' => 'float',
        'imputed_this_year' => 'float',
        'transferred_this_year' => 'float',
        'expired_this_year' => 'float',
    ];

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

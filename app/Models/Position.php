<?php

namespace App\Models;

use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model implements IModel
{
    /** @use HasFactory<\Database\Factories\PositionFactory> */
    use HasFactory;

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'label',
        'classification_id',
        'description',
        'planned_headcount',
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
            'code' => ['required', 'unique:' . $this->getTable(), 'max:255'],
            'planned_headcount' => ['nullable', 'integer'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'label' => ['sometimes', 'required', IModel::IGNORE_RULE],
            'code' => ['sometimes', 'required', IModel::IGNORE_RULE],
            'planned_headcount' => ['sometimes', 'integer'],
            "id" => "required|exists:" . $this->getTable() . ",$this->primaryKey"
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['classification','assignment', 'employe'];

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function employe()
    {
        return $this->hasMany(Employe::class);
    }

    public function assignment()
    {
        return $this->hasMany(Assignment::class);
    }
}

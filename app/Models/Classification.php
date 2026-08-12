<?php

namespace App\Models;

use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classification extends Model implements IModel
{
    /** @use HasFactory<\Database\Factories\ClassificationFactory> */
    use HasFactory;

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'label',
        'parent_id',
        'order',
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
            'order' => 'integer',
            'parent_id' => 'nullable|exists:' . $this->getTable() . ',id',
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
            'order' => 'sometimes|integer',
            "id" => "required|exists:" . $this->getTable() . ",$this->primaryKey"
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['parent_level', 'children', 'positions'];


    public function parent_level()
    {
        return $this->belongsTo(Classification::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Classification::class, 'parent_id');
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}

<?php

namespace App\Models;

use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model implements IModel
{

    /** @use HasFactory<\Database\Factories\TitleFactory> */
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
        'is_active',
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
            'parent_id' => 'nullable|exists:' . $this->getTable() . ',id',
            'is_active' => 'boolean',
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
            'is_active' => ['sometimes', IModel::IGNORE_RULE],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['parent_level', 'children'];

    public function parent_level()
    {
        return $this->belongsTo(Unit::class);
    }

    public function children()
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    public function assignment()
    {
        return $this->hasMany(Assignment::class, 'unit_id');
    }
}

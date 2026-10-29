<?php

namespace App\Models;

use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model implements IModel
{
    /** @use HasFactory<\Database\Factories\PositionFactory> */
    use HasFactory;

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'contry_code',
        'label',
        'date',
        'is_recurring',
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
            'country_code' => ['required', 'max:2'],
            'label' => ['required', 'max:255'],
            'date' => ['required', 'date'],
            'is_recurring' => ['required', 'boolean'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'country_code' => ['sometimes', 'max:2'],
            'label' => ['sometimes'],
            'date' => ['sometimes', 'date'],
            'is_recurring' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = [];

}


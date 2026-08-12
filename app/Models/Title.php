<?php

namespace App\Models;

use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Title extends Model implements IModel
{
    /** @use HasFactory<\Database\Factories\TitleFactory> */
    use HasFactory;

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'label',
        'code',
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
            'sequence' => 'integer'
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
            "id" => "required|exists:" . $this->getTable() . ",$this->primaryKey"
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['employe'];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}

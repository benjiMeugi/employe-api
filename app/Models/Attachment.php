<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    /** @use HasFactory<\Database\Factories\AttachmentFactory> */
    use HasFactory;

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'file_reference',
        'document_type',
        'attachable_type',
        'attachable_id',
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
     * (file_reference n'apparaît jamais ici : le client envoie "file",
     * jamais le chemin déjà calculé — celui-ci est toujours déterminé
     * côté serveur, après l'upload.)
     */
    public function rules()
    {
        return [
            'file' => ['required', 'file', 'max:10240'], // 10 Mo max
            'document_type' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     * (seul document_type a du sens à modifier après coup — le fichier
     * et son rattachement ne devraient pas changer)
     */
    public function update_rules()
    {
        return [
            'document_type' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['attachable'];

    public function attachable()
    {
        return $this->morphTo();
    }
}

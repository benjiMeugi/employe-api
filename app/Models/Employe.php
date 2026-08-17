<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory;

    public static $GENDER_OPTIONS = ['M','F'];

        /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'registration_number',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'hire_date',
        'status',
        'professional_email',
        'personal_email',
        'phone_number1',
        'phone_number2',
        'title_id',
        'classification_id',
        'position_id',
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
            'registration_number' => ['required', 'unique:' . $this->getTable(), 'max:255'],
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:' . implode(',', self::$GENDER_OPTIONS)],
            'hire_date' => ['required', 'date'],
            'status' => ['required', 'boolean'],
            'professional_email' => ['nullable', 'email', 'unique:' . $this->getTable(), 'max:255'],
            'personal_email' => ['nullable', 'email', 'unique:' . $this->getTable(), 'max:255'],
            'phone_number1' => ['nullable',],
            'phone_number2' => ['nullable',],
            'title_id' => ['required', 'exists:' . (new Title)->getTable() . ',id'],
            'classification_id' => ['required', 'exists:' . (new Classification)->getTable() . ',id'],
            'position_id' => ['required', 'exists:' . (new Position)->getTable() . ',id']
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'first_name' => ['sometimes'],
            'last_name' => ['sometimes'],
            'birth_date' => ['sometimes', 'date'],
            'gender' => ['sometimes'],
            'hire_date' => ['sometimes', 'date'],
            'status' => ['sometimes'],
            'professional_email' => ['sometimes'],
            'personal_email' => ['sometimes', 'email'],
            'phone_number1' => ['sometimes'],
            'phone_number2' => ['sometimes'],
            'title_id' => ['sometimes'],
            'classification_id' => ['sometimes'],
            'position_id' => ['sometimes']
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['title', 'classification', 'position', 'career_event'];

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function career_event()
    {
        return $this->hasMany(CareerEvent::class, 'employee_id');
    }
}

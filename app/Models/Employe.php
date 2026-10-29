<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\AuthApiService;
use BenjiMeugi\Contracts\IModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employe extends Model implements IModel
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
            'phone_number1' => ['nullable'],
            'phone_number2' => ['nullable'],
            'title_id' => ['required', 'exists:' . (new Title)->getTable() . ',id'],
            'classification_id' => ['nullable', 'exists:' . (new Classification)->getTable() . ',id'],
            'position_id' => ['nullable', 'exists:' . (new Position)->getTable() . ',id']
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'birth_date' => ['sometimes', 'date'],
            'gender' => ['sometimes', 'in:' . implode(',', self::$GENDER_OPTIONS)],
            'hire_date' => ['sometimes', 'date'],
            'status' => ['sometimes', 'boolean'],
            'professional_email' => ['sometimes', 'nullable', 'email'],
            'personal_email' => ['sometimes', 'nullable', 'email'],
            'phone_number1' => ['sometimes', 'nullable', 'string'],
            'phone_number2' => ['sometimes', 'nullable', 'string'],
            'title_id' => ['sometimes', 'exists:' . (new Title)->getTable() . ',id'],
            'classification_id' => ['sometimes', 'nullable', 'exists:' . (new Classification)->getTable() . ',id'],
            'position_id' => ['sometimes', 'nullable', 'exists:' . (new Position)->getTable() . ',id']
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['title', 'classification', 'position', 'contracts', 'payslips', 'careerEvent'];

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

    public function careerEvent()
    {
        return $this->hasMany(CareerEvent::class, 'employee_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'employee_id');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class, 'employee_id');
    }

    protected static function booted(): void
    {
        // Création d'un utilisateur dans l'API d'authentification à la création d'un employé
        static::creating(function (Employe $employee) {
            if (! $employee->professional_email) {
                return;
            }

            try {
                $userId = (new AuthApiService())->createUser(
                    username: $employee->professional_email,
                    email: $employee->professional_email,
                    firstName: $employee->first_name,
                    lastName: $employee->last_name,
                );

                // Affectation directe (non mass assignable)
                $employee->user_id = $userId;
            } catch (\Throwable $e) {
                throw $e;
            }
        });

        // Synchronisation de l'état de l'utilisateur avec le statut de l'employé
        static::updating(function (Employe $employee) {
            if ($employee->isDirty('status') && $employee->user_id) {
                try {
                    (new AuthApiService())->setUserEnabled(
                        $employee->user_id,
                        (bool) $employee->status
                    );
                } catch (\Throwable $e) {
                    // Ne pas bloquer la mise à jour si l'API d'authentification est indisponible
                }
            }
        });

        // Suppression de l'utilisateur dans l'API d'authentification
        static::deleting(function (Employe $employee) {
            if ($employee->user_id) {
                try {
                    (new AuthApiService())->deleteUser($employee->user_id);
                } catch (\Throwable $e) {
                    // Ne pas bloquer la suppression si l'API d'authentification est indisponible
                }
            }
        });
    }

}

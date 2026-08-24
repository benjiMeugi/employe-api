<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Payslip extends Model 
{ 
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory;

    public static $STATUS_OPTIONS = ['Pending','Validated','Paid'];

        /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = ['employee_id', 'contract_id', 'period', 'issue_date', 'gross_salary', 'total_earnings', 'total_deductions', 'net_pay', 'status'];

    

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
            'employee_id' => ['required', 'exists:' . (new Employe)->getTable() . ',id'],
            'contract_id' =>['required', 'exists:' . (new Contract)->getTable() . ',id'],
            'period' => ['required', 'date_format:Y-m'],
            'issue_date' => ['required', 'date'],
            'gross_salary' => ['required', 'numeric'],
            'total_earnings' => ['required', 'numeric'],
            'status' => ['required', 'in:' . implode(',', self::$STATUS_OPTIONS)],
            'total_deductions' => ['required', 'numeric'],
            'net_pay' => ['required', 'numeric'],
            
        ];
    }

    /**
     * Get the validation rules for the model when updating.
     */
    public function update_rules()
    {
        return [
            'employee_id' => ['sometimes', 'exists:' . (new Employe)->getTable() . ',id'],
            'contract_id' => ['sometimes', 'exists:' . (new Contract)->getTable() . ',id'],
            'period' => ['sometimes', 'date_format:Y-m'],
            'issue_date' => ['sometimes','date'],
            'gross_salary' => ['sometimes', 'numeric'],
            'status' => ['sometimes', 'in:' . implode(',', self::$STATUS_OPTIONS)],
            'total_deductions' => ['sometimes', 'numeric'],
            'total_earnings' => ['sometimes', 'numeric'],
            'net_pay' => ['sometimes', 'numeric'],
            
        ];
    }

    /**
     * Get the relation methods for the model.
     */
    public $relation_methods = ['employe', 'contract', 'payslipLine'];

    public function Employe(): BelongsTo
    {
       return $this->belongsTo(Employe::class);  
    } 

    public function contract(): BelongsTo 
    { 
        return $this->belongsTo(Contract::class); 
    }

    public function lines(): HasMany 
    { 
        return $this->hasMany(PayslipLine::class); 
    }

    public function payslipLine(): HasMany
    {
        return $this->lines();
    }


}

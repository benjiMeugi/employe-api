<?php

namespace database\seeders;

use App\Models\Contract;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        Contract::create([
            'employee_id' => 1,
            'contract_type_id' => 1,
            'start_date' => "2018-04-20",
            'end_date' => null,
            'pay_frequency' => 'Monthly',
            'base_salary' => 500000,
            'status' => 'Active',
        ]);
    }
}


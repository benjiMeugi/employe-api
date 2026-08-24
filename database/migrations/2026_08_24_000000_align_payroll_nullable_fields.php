<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->date('issue_date')->nullable()->change();
        });

        Schema::table('payslip_lines', function (Blueprint $table) {
            $table->decimal('calculation_base', 15, 2)->nullable()->change();
            $table->decimal('rate', 5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->date('issue_date')->nullable(false)->change();
        });

        Schema::table('payslip_lines', function (Blueprint $table) {
            $table->decimal('calculation_base', 15, 2)->nullable(false)->change();
            $table->decimal('rate', 5, 2)->default(100.00)->nullable(false)->change();
        });
    }
};
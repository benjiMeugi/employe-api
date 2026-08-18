<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employes')->onDelete('cascade');

            $table->foreignId('absence_type_id')->constrained('absence_types');
            $table->integer('year');
            $table->decimal('cumulative_acquired_days', 5, 2)->default(0);
            $table->decimal('consumed_days', 5, 2)->default(0);
            $table->decimal('expired_days', 5, 2)->default(0);
            $table->decimal('available_balance', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['employee_id', 'absence_type_id', 'year'], 'emp_abs_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};

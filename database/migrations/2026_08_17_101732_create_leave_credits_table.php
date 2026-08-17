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
        Schema::create('leave_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained("employees")->cascadeOnDelete();
            $table->foreignId('absence_type_id')->constrained("absence_types")->cascadeOnDelete();
            $table->string("period");
            $table->double("acquired_days");
            $table->date("acquisition_date");
            $table->date("expiration_date");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_credits');
    }
};

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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absence_type_id')->constrained('absence_types');
            $table->foreignId('absence_request_id')->nullable()->constrained('absence_requests')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count');
            $table->boolean('is_deductible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};

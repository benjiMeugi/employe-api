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
        Schema::create('promotions', function (Blueprint $table) {
            $table->foreignId('id')->primary()->constrained('career_events')->cascadeOnDelete();
            $table->foreignId('previous_position_id')->nullable()
                ->constrained('positions')->nullOnDelete();
            $table->foreignId('new_position_id')->nullable()
                ->constrained('positions')->nullOnDelete();
            $table->foreignId('previous_classification_id')->nullable()
                ->constrained('classifications')->nullOnDelete();
            $table->foreignId('new_classification_id')->nullable()
                ->constrained('classifications')->nullOnDelete();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};

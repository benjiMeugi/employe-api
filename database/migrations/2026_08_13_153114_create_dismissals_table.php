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
        Schema::create('dismissals', function (Blueprint $table) {
            $table->foreignId('id')->primary()->constrained('career_events')->cascadeOnDelete();
            $table->string('reason');
            $table->date('deffective_date');
            $table->double('severance_pay')->nullable();
            $table->bigInteger('notice_days')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dismissals');
    }
};

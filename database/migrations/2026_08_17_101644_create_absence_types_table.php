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
        Schema::create('absence_types', function (Blueprint $table) {
            $table->id();
            $table->string("code")->unique();
            $table->string("label");
            $table->boolean("is_paid")->default(false);
            $table->boolean("is_cumulative")->default(false);
            $table->integer("max_cumulative_years")->nullable();
            $table->double("day_cap")->nullable();
            $table->integer("expiration_delay_months")->nullable();
            $table->boolean("requires_supporting_document")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_types');
    }
};

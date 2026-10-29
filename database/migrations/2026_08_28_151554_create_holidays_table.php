<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();

            // Mono-pays pour l'instant ("TG" partout), mais la colonne
            // existe dès maintenant — l'ajouter plus tard sur une table
            // déjà remplie serait plus pénible.
            $table->string('country_code', 2)->default('TG');

            $table->string('label');
            $table->date('date');

            // Sert uniquement à la génération des années suivantes,
            // jamais au calcul d'une absence.
            $table->boolean('is_recurring')->default(false);
            $table->timestamps();

            $table->unique(['country_code', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};

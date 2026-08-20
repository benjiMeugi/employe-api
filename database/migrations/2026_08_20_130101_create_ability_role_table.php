<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nom "ability_role" : convention Laravel pour les tables pivot
     * (les deux noms de modèles, singulier, ordre alphabétique) —
     * permet à belongsToMany() de la retrouver automatiquement,
     * sans avoir à préciser le nom de table dans les modèles.
     */
    public function up(): void
    {
        Schema::create('ability_role', function (Blueprint $table) {
            $table->foreignId('ability_id')->constrained('abilities')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['ability_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ability_role');
    }
};

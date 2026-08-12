<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            // On change l'enum en string pour accepter 'paye' et d'autres futurs statuts
            $table->string('statut')->change();
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            // Retour à l'état initial si besoin (ajoute tes anciens statuts ici)
            $table->enum('statut', ['en_attente', 'confirmee', 'annulee'])->change();
        });
    }
};
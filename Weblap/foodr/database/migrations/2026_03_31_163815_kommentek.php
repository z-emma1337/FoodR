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
    Schema::create('kommentek', function (Blueprint $table) {
        $table->id();
        $table->foreignId('felhasznalo_id')->constrained('felhasznalo')->cascadeOnDelete();
        $table->text('felhasznalo_nev');
        $table->text('pfpurl');
        $table->foreignId('recept_id')->constrained('recept')->cascadeOnDelete();
        $table->text('komment');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kommentek');
    }
};

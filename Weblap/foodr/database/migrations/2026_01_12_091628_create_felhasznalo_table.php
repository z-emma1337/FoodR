<?php

// database/migrations/2026_01_12_000004_create_felhasznalo_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('felhasznalo', function (Blueprint $table) {
            $table->id();
            $table->string('nev')->nullable();
            $table->string('email')->unique();
            $table->string('jelszo');
            $table->foreignId('allergen_id')->constrained('allergen')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('felhasznalo');
    }
};

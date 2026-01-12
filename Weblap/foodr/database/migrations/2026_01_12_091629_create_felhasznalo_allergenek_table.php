<?php

// database/migrations/2026_01_12_000005_create_felhasznalo_allergenek_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('felhasznalo_allergenek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('felhasznalo_id')->constrained('felhasznalo')->cascadeOnDelete();
            $table->foreignId('allergen_id')->constrained('allergen')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('felhasznalo_allergenek');
    }
};

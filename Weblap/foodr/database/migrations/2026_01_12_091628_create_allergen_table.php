<?php

// database/migrations/2026_01_12_000002_create_allergen_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('allergen', function (Blueprint $table) {
            $table->id();
            $table->string('nev', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allergen');
    }
};


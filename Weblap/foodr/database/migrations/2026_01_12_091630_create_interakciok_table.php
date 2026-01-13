<?php

// database/migrations/2026_01_12_000008_create_interakciok_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('interakciok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('felhasznalo_id')->constrained('felhasznalo')->cascadeOnDelete();
            $table->foreignId('recept_id')->constrained('recept')->cascadeOnDelete();
            $table->boolean('liked')->default(false);
            $table->boolean('mentett')->default(false);
            $table->unsignedTinyInteger('vote')->default(0);
            $table->timestamps();
            $table->unique(['felhasznalo_id','recept_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interakciok');
    }
};


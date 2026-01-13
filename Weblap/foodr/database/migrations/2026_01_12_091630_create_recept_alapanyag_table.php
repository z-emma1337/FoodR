<?php

// database/migrations/2026_01_12_000007_create_recept_alapanyag_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recept_alapanyag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recept_id')->constrained('recept')->cascadeOnDelete();
            $table->foreignId('alapanyag_id')->constrained('alapanyag')->cascadeOnDelete();
            $table->string('adag', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recept_alapanyag');
    }
};


<?php

// database/migrations/2026_01_12_000003_create_alapanyag_allergenek_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alapanyag_allergenek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alapanyag_id')->constrained('alapanyag')->cascadeOnDelete();
            $table->foreignId('allergen_id')->constrained('allergen')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alapanyag_allergenek');
    }
};

<?php

// database/migrations/2026_01_12_000011_create_recept_stat_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recept_stat', function (Blueprint $table) {
            $table->foreignId('recept_id')->constrained('recept')->cascadeOnDelete()->primary();
            $table->integer('megtekintesek')->default(0);
            $table->integer('mentesek')->default(0);
            $table->integer('likeok')->default(0);
            $table->integer('voteok')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recept_stat');
    }
};

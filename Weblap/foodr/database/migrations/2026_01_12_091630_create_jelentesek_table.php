<?php

// database/migrations/2026_01_12_000009_create_jelentesek_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jelentesek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recept_id')->constrained('recept')->cascadeOnDelete();
            $table->foreignId('felhasznalo_id')->nullable()->constrained('felhasznalo')->nullOnDelete();
            $table->text('ok')->nullable();
            $table->string('statusz', 50)->nullable();
            $table->string('datum', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jelentesek');
    }
};

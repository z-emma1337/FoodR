<?php

// database/migrations/2026_01_12_000010_create_naplo_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('naplo', function (Blueprint $table) {
            $table->id();
            $table->string('esemeny')->nullable();
            $table->json('adat')->nullable();
            $table->string('ido', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('naplo');
    }
};

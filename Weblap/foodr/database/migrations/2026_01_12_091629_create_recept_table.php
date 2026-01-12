<?php

// database/migrations/2026_01_12_000006_create_recept_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recept', function (Blueprint $table) {
            $table->id();
            $table->string('nev');
            $table->text('leiras')->nullable();
            $table->integer('ido')->nullable();
            $table->integer('adag')->nullable();
            $table->string('kep_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recept');
    }
};


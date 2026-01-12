<?php

// database/migrations/2026_01_12_000001_create_alapanyag_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alapanyag', function (Blueprint $table) {
            $table->id();
            $table->string('nev');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alapanyag');
    }
};


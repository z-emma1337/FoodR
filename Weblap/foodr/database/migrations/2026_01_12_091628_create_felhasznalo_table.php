<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('felhasznalo', function (Blueprint $table) {
            $table->id();
            $table->string('nev');
            $table->string('email')->unique();
            $table->string('jelszo');

            // foreign key, de lehet null, ha nincs allergén kiválasztva
            $table->foreignId('allergen_id')
                ->nullable()
                ->constrained('allergenek')
                ->nullOnDelete();
            $table->foreign('allergen_id')->references('id')->on('allergen')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('felhasznalo');
    }
};

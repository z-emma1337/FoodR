<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('felhasznalo', function (Blueprint $table) {
        $table->string('profilkepurl')
              ->default('imgs/Profilkepek/avatar.png')
              ->after('password'); // vagy ahova akarod
    });
}

public function down()
{
    Schema::table('felhasznalo', function (Blueprint $table) {
        $table->dropColumn('profilkepurl');
    });
}
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('felhasznalo', function (Blueprint $table) {
        $table->dropForeign(['allergen_id']); 
        $table->dropColumn('allergen_id');
    });
}

public function down()
{
    Schema::table('felhasznalo', function ($table) {
        $table->unsignedBigInteger('allergen_id')->nullable();
    });
}
};

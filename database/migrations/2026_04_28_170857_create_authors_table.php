<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');                           // To'liq ismi
            $table->string('birth_date')->nullable();         // Tug'ilgan sanasi (masalan: "10.04.1894")
            $table->string('death_date')->nullable();         // Vafot sanasi (NULL bo'lsa - hayot)
            $table->string('birth_place')->nullable();        // Tug'ilgan joyi
            $table->string('nationality')->nullable();        // Millati
            $table->text('biography')->nullable();            // Hayoti va ijodi
            $table->text('legacy')->nullable();               // Merosi
            $table->string('image_url')->nullable();          // Surati
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');        // Kitob nomi
            $table->foreignId('author_id')  // Muallif (one-to-many)
                  ->constrained('authors')
                  ->cascadeOnDelete();
            $table->integer('price');       // Narxi
            $table->integer('stock');       // Qoldiq (soni)
            $table->string('genre');        // Janr
            $table->integer('year');        // Nashr yili
            $table->text('description');    // Tavsif
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};

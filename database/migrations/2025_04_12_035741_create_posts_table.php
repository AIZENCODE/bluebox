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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            $table->string('title');             // Título del post
            $table->string('slug')->unique();   // Slug para URL
            $table->text('excerpt')->nullable(); // Resumen corto
            $table->longText('body')->nullable();           // Contenido principal
            
            $table->string('image_url')->nullable(); // Ruta imagen destacada
            $table->boolean('is_published')->default(false); // Estado de publicación
            $table->timestamp('published_at')->nullable();   // Fecha publicación
            
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade'); // Autor del post

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();

            $table->string('type')->nullable(); // Tipo de log: error, warning, info, etc.
            $table->string('source')->nullable(); // De dónde viene el error (opcional)
            $table->text('message'); // Mensaje del error o log
            $table->text('trace')->nullable(); // Traza completa (stack trace) si quieres guardar detalles
            $table->json('context')->nullable(); // Información adicional en JSON

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};

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
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();

            // Relaciones con currencies
            $table->foreignId('from_currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            $table->foreignId('to_currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            // Tipo de cambio
            $table->decimal('rate', 18, 6); // Valor del tipo de cambio
            $table->date('date');           // Fecha del tipo de cambio


            // Opcional: evitar duplicados
            $table->unique(['from_currency_id', 'to_currency_id', 'date']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};

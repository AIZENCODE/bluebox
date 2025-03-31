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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->text('descripcion')->nullable(); 
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('etapa', ['Planificación', 'Ejecución', 'Monitoreo y Control', 'Cierre'])->default('Planificación');
            $table->boolean('estado')->default(true);
            $table->foreignId('proyect_id')->constrained('contracts');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

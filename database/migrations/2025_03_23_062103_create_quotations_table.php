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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 20)->unique();
            $table->date('fecha_creacion');
            $table->integer('days');
            $table->enum('etapa', ['borrador', 'enviada', 'aceptada', 'rechazada'])->default('borrador');
            $table->boolean('estado')->default(true);
        
            $table->foreignId('igv_id')->constrained('igvs');
            $table->foreignId('companie_id')->constrained('companies');

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};

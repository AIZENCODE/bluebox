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
        Schema::create('data', function (Blueprint $table) {

            $table->id();

            $table->string('nombre');
            $table->string('razon_social');
            $table->string('ruc');
            $table->string('imagen_url')->nullable();
            $table->string('telefono_uno')->nullable();
            $table->string('telefono_dos')->nullable();
            $table->string('correo_uno')->nullable();
            $table->string('correo_dos')->nullable();
            $table->string('direccion_uno')->nullable();
            $table->string('direccion_dos')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};

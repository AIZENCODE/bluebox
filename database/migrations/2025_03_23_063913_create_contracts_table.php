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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            $table->string('code', 20)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->boolean('state')->default(true);
            // Aquí definimos el campo 'etapa' como enum en la BD
            $table->enum('stage', ['inicio', 'proceso', 'finalizado'])->default('inicio');

            $table->foreignId('quatation_id')->nullable()->constrained('quotations');
            $table->foreignId('companie_id')->constrained('companies');

            $table->foreignId('igv_id')->nullable()->constrained('igvs');
            $table->foreignId('currency_id')->nullable()->constrained('currencies');

            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('user_update_id')->nullable()->constrained('users', 'id');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};

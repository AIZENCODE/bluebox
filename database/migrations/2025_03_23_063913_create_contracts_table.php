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
            $table->string('name');
            $table->string('document')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('state')->default(true);
            // Aquí definimos el campo 'etapa' como enum en la BD
            $table->enum(
                'stage',
                [
                    'pendiente',
                    'enviado',
                    'inicio',
                    'proceso',
                    'finalizado',
                    'cancelado'
                ]
            )->default('pendiente');

            $table->boolean('mail')->default(false);
            $table->date('mail_date')->nullable();



            $table->foreignId('quotation_id')->nullable()->constrained('quotations');
            $table->foreignId('companie_id')->constrained('companies');

            $table->foreignId('igv_id')->nullable()->constrained('igvs');
            $table->foreignId('currency_id')->nullable()->constrained('currencies');

            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('user_update_id')->nullable()->constrained('users', 'id');


            $table->timestamps();
            $table->softDeletes();
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

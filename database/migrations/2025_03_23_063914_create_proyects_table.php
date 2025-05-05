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
        Schema::create('proyects', function (Blueprint $table) {
            $table->id();

            $table->string('code', 20)->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->enum('stage', [
                'iniciado',
                'en_proceso',
                'en_desarrollo',
                'en_pruebas',
                'en_espera',
                'en_espera_revision',
                'seguimiento',
                'finalizado'
            ])->default('iniciado');

            $table->boolean('state')->default(true);


            // $table->boolean('mail')->default(false);
            $table->date('mail_date')->nullable();

            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();

            $table->foreignId('contract_id')
                ->nullable()
                ->constrained('contracts')
                ->onDelete('cascade');

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
        Schema::dropIfExists('proyects');
    }
};

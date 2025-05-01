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
                'planificacion',
                'ejecucion',
                'seguimiento',
                'finalizado'
            ])->default('planificacion');

            $table->boolean('state')->default(true);

            $table->foreignId('contract_id')
            ->nullable()
            ->constrained('contracts')
            ->onDelete('cascade');

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
        Schema::dropIfExists('proyects');
    }
};

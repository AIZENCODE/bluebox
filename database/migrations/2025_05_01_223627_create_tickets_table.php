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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique()->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('document')->nullable();



            $table->enum('priority', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('status', ['abierto', 'en_progreso', 'resuelto', 'cerrado'])->default('abierto');

            $table->timestamp('due_date')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->foreignId('proyect_id')->constrained()->onDelete('cascade');
     
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
        Schema::dropIfExists('tickets');
    }
};

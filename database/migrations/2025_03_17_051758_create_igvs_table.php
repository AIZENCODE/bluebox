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
        Schema::create('igvs', function (Blueprint $table) {
            $table->id();

            $table->string('type');
            $table->decimal('percentage', 5, 2); // Ej: 18.00

            // $table->foreignId('user_id')->nullable()->constrained('users');
            // $table->foreignId('user_update_id')->nullable()->constrained('users', 'id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('igvs');
    }
};

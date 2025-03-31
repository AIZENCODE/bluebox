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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('numero');
            $table->string('numero_interbancario')->nullable();
            $table->boolean('estado')->default(true);
            
            $table->foreignId('bank_id')->constrained('banks');
            $table->foreignId('accounttype_id')->constrained('account_types');
            $table->foreignId('currency_id')->constrained('currencies');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

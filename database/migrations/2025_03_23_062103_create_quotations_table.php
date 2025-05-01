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

            $table->string('code', 20)->unique();
            $table->date('creation_date');
            $table->integer('days');
            $table->enum('stage', ['borrador', 'enviada', 'aceptada', 'rechazada'])->default('borrador');
            $table->boolean('state')->default(true);

            $table->foreignId('igv_id')->constrained('igvs');
            $table->foreignId('companie_id')->constrained('companies');
            $table->foreignId('currency_id')->constrained('currencies');

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
        Schema::dropIfExists('quotations');
    }
};

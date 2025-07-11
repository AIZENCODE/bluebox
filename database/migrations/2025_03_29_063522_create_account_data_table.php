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
        Schema::create('account_data', function (Blueprint $table) {
            $table->id();

            $table->foreignId('data_id')
                ->constrained('data')
                ->onDelete('cascade');

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_data');
    }
};

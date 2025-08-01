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

            $table->string('name');
            $table->string('number');
            $table->string('interbank_number')->nullable();
            $table->boolean('state')->default(true);

            $table->foreignId('bank_id')->constrained('banks');
            $table->foreignId('accounttype_id')->constrained('account_types');
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
        Schema::dropIfExists('accounts');
    }
};

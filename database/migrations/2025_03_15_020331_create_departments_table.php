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
        Schema::create('departments', function (Blueprint $table) {
            $table->unsignedBigInteger('idDepartment')->primary(); // No autoincremental

            $table->string('department', 100);
            $table->timestamps();

            $table->unsignedBigInteger('idCountry');
            $table->foreign('idCountry')
            ->references('idCountry')
            ->on('countries')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

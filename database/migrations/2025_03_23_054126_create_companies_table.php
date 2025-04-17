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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('razon_social');
            $table->string('ruc');
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('estado')->default(true);

            $table->foreignId('idCountry')->nullable()->constrained('countries', 'idCountry');
            $table->foreignId('idDepartment')->nullable()->constrained('departments', 'idDepartment');
            $table->foreignId('idProvince')->nullable()->constrained('provinces', 'idProvince');
            $table->foreignId('idDistrict')->nullable()->constrained('districts', 'idDistrict');

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
        Schema::dropIfExists('companies');
    }
};

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
            $table->boolean('is_operator')->default(false);
            $table->string('name');
            $table->string('address_street')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('inn')->nullable();
            $table->bigInteger('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->integer('bank_mfo')->nullable();
            $table->string('director_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('license_number')->nullable();
            $table->timestamps();
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

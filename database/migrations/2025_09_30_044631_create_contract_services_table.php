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
        Schema::create('contract_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->morphs('serviceable'); // serviceable_type, serviceable_id
            
            // Specific pricing for different service types
            $table->json('pricing_structure')->nullable(); // Flexible pricing structure
            
            // Contract service specific terms
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('specific_terms')->nullable();
            
            $table->timestamps();
            
            // Ensure unique contract-service combinations
            $table->unique(['contract_id', 'serviceable_type', 'serviceable_id'], 'contract_service_unique');
            $table->index(['serviceable_type', 'serviceable_id', 'is_active'], 'contract_service_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_services');
    }
};

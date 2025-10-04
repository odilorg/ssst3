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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_company_id')->constrained('companies')->onDelete('cascade');
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('terms')->nullable();
            $table->json('pricing_structure')->nullable(); // Global contract terms
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('draft');
            $table->string('signed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['supplier_company_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};

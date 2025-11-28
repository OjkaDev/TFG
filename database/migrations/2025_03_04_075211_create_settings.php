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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('job_name')->default('JobName');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con users
            $table->decimal('salary_per_hour', 8, 2)->default(0.0);
            $table->decimal('contract_hour', 8, 2)->default(90);
            $table->time('night_hours_start')->nullable();
            $table->time('night_hours_end')->nullable();
            $table->decimal('night_salary', 8, 2)->nullable();
            $table->decimal('extra_salary', 8, 2)->nullable();
            $table->decimal('salary_extra_hours', 8, 2)->nullable();
            $table->decimal('tax_percentage',4,2)->default(4);
            $table->timestamps(); 
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

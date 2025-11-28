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
        Schema::create('computed_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('payroll')->unique();
            $table->decimal('total_hour', 8, 2)->default(0);
            $table->decimal('total_night_hour', 8, 2)->default(0);
            $table->decimal('extra_hours', 8, 2)->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computed_hours');
    }
};

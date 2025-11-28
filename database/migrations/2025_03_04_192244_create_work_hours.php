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
        Schema::create('work_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con users
            $table->foreignId('setting_id')->nullable()->constrained('settings')->onDelete('cascade'); // Relación con settings
            $table->datetime('hour_start');
            $table->datetime('hour_end');
            $table->string('payroll')->nullable();
            $table->boolean('is_vacation')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_hours');
    }
};

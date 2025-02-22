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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->integer('business_id');
            $table->integer('user_id');
            $table->enum('type', ['self_employed', 'salary_based']);
            $table->decimal('base_fee', 8, 2)->default(25.00); // Fixed job fee
            $table->decimal('per_mile_rate', 8, 2)->nullable(); // Additional per-mile rate after 60 miles        
            $table->decimal('commission_rate', 5, 2)->nullable(); // For self-employed drivers
            $table->decimal('fixed_salary', 10, 2)->nullable(); // For salary-based drivers
            $table->string('license_number')->unique();
            $table->date('license_expiry'); // Driving License Expiry Date
            $table->string('dvla_report')->nullable(); // DVLA Report File Path
            $table->string('insurance_policy_number')->nullable(); // Insurance Policy Number
            $table->date('insurance_expiry')->nullable(); // Insurance Expiry Date
            $table->boolean('owns_vehicle')->default(false); // Self-employed drivers may use their own vehicle
            $table->integer('years_of_experience')->nullable(); // Driving Experience in Years
            $table->text('certifications')->nullable(); 
            $table->boolean('available')->default(true)->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};

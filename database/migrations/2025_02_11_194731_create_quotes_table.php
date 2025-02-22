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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->integer('business_id');
            $table->integer('customer_id');
            $table->integer('vehicle_type_id');
            $table->json('pickup_locations'); // Array of pickup points (text, latitude, longitude)
            $table->json('stops')->nullable(); // Optional stops before drop-off (text, latitude, longitude)
            $table->json('dropoff_locations'); // Drop-off points (text, latitude, longitude)
            $table->decimal('estimated_distance', 8, 2); // Estimated total distance in miles
            $table->decimal('estimated_fare', 10, 2)->nullable(); // Estimated fare based on distance
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};

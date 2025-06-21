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
            $table->integer('vehicle_type_id'); // 
            $table->integer('driver_id')->nullable(); // Optional driver assignment
            // $table->integer('goods_type_id')->default(9); // Default to 'Vehicles'
            $table->integer('transport_type_id')->nullable()->comment('car, van, truck, etc');
            $table->decimal('estimated_distance', 8, 2); // Estimated total distance in miles
            $table->decimal('estimated_fare', 10, 2)->nullable(); // Estimated fare based on distance
            $table->timestamp('pickup_time')->nullable();

            $table->json('stops')->nullable(); // Optional stops before drop-off (text, latitude, longitude)

            // collection details
            $table->enum('collection_place_type',['garage','dealership','house','auto','company','branch','shop','other'])->default('other');
            $table->string('collection_contact_name')->nullable();
            $table->string('collection_contact_phone')->nullable();
            $table->string('collection_contact_email')->nullable();
            $table->json('pickup_locations'); // Array of pickup points (text, latitude, longitude)

            // delivery details
            $table->enum('delivery_place_type',['garage','dealership','house','auto','company','branch','shop','other'])->default('other');
            $table->string('delivery_contact_name')->nullable();
            $table->string('delivery_contact_phone')->nullable();
            $table->string('delivery_contact_email')->nullable();
            $table->json('dropoff_locations'); // Drop-off points (text, latitude, longitude)

            //  Vehicle availability date and time seaprete column
            $table->timestamp('vehicle_available_from')->nullable();
            $table->timestamp('vehicle_available_to')->nullable();

            // vehicle details
            $table->string('vehicle_make')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('number_plate')->nullable();
            $table->string('gearbox')->nullable();
            $table->integer('seating_capacity')->nullable();
            $table->longText('comments')->nullable();
            
            // Payment details
            $table->string('payment_link_id')->nullable();
            $table->string('payment_link_url')->nullable();
            
            $table->string('payment_method')->nullable();
            $table->json('payment_details')->nullable();
            
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->decimal('amount_due', 10, 2)->default(0.00);
            $table->enum('payment_status', ['pending', 'paid', 'partially_paid', 'failed','due','authorised','refunded'])->default('pending');
            $table->enum('status', ['pending','confirmed' ,'assigned', 'cancelled','in_transit','delivered','completed','failed'])->default('pending');
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

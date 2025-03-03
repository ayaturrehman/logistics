<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transport_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->integer('goods_type_id');
            $table->timestamps();
        });

        DB::table('transport_types')->insert([
            ['name' => 'Car', 'goods_type_id' => 9],
            ['name' => 'Sports Car', 'goods_type_id' => 9],
            ['name' => 'Small Van', 'goods_type_id' => 9],
            ['name' => 'Large Van', 'goods_type_id' => 9],
            ['name' => '4×4 SUV', 'goods_type_id' => 9],
            ['name' => 'Boat', 'goods_type_id' => 9],
            ['name' => 'Motor Bike', 'goods_type_id' => 9],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_types');
    }
};

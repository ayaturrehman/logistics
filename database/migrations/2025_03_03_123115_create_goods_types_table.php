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
        Schema::create('goods_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('goods_types')->insert([
            ['name' => 'Furniture'],
            ['name' => 'Machine Parts'],
            ['name' => 'Perishables'],
            ['name' => 'Heavy Machinery'],
            ['name' => 'Electronics'],
            ['name' => 'Vehicles'], // If selected, ask for vehicle type
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_types');
    }
};

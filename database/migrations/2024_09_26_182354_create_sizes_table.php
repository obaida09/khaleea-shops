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
        Schema::create('sizes', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define 'id' as a UUID
            $table->string('name');
            $table->timestamps();
        });

        // Create pivot table for linking sizes to products
        Schema::create('product_size', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('size_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_size');
        Schema::dropIfExists('sizes');
    }
};

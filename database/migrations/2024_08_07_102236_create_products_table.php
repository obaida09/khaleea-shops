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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('quantity')->default(0);
            $table->json('colors')->nullable();
            $table->json('sizes')->nullable();
            $table->enum('season', ['winter', 'summer', 'all'])->default('all');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->uuid('shop_id');
            $table->uuid('category_id');

            // Foreign key constraintse
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

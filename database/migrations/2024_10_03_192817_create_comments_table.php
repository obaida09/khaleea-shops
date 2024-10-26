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
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('content'); // The content of the comment
            $table->timestamps();

            $table->uuid('user_id');
            $table->uuid('post_id');
            $table->uuid('parent_id')->nullable();

              // Foreign key constraints
              $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
              $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
              $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

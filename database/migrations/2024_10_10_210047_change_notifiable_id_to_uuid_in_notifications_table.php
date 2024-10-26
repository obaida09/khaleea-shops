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
        Schema::table('notifications', function (Blueprint $table) {
            // Update the notifiable_id column to char(36) to support UUID
            $table->char('notifiable_id', 36)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Revert notifiable_id to unsignedBigInteger if needed
            $table->unsignedBigInteger('notifiable_id')->change();
        });
    }
};

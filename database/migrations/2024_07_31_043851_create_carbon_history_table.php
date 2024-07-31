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
        Schema::create('carbon_history', function (Blueprint $table) {
            $table->uuid('guid')->primary();
            $table->string('type');
            $table->integer('old_km');
            $table->integer('new_km');
            $table->integer('km_diff');
            $table->integer('carbon_total');
            $table->char('user_guid',36)->index();
            $table->foreign('user_guid')->references('guid')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carbon_history');
    }
};

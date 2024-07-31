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
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('guid')->primary();
            $table->char('user_guid',36)->index();
            $table->char('offer_guid',36)->index();
            $table->string('code');
            $table->foreign('user_guid')->references('guid')->on('users')->onDelete('cascade');
            $table->foreign('offer_guid')->references('guid')->on('offers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

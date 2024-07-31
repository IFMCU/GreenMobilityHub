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
        Schema::create('table_otps', function (Blueprint $table) {
            $table->uuid('guid')->primary();
            $table->string('otp');
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
        Schema::dropIfExists('table_otps');
    }
};

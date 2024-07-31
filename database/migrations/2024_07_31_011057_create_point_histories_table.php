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
        Schema::create('point_histories', function (Blueprint $table) {
            $table->uuid('guid')->primary();
            $table->string('total');
            $table->integer('point');
            $table->string('file_url')->nullable();
            $table->char('point_category_guid',36)->index();
            $table->char('user_guid',36)->index();
            $table->enum('status',['aprooved', 'waiting', 'rejected'])->default('waiting');
            $table->foreign('point_category_guid')->references('guid')->on('point_categories')->onDelete('cascade');
            $table->foreign('user_guid')->references('guid')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_histories');
    }
};

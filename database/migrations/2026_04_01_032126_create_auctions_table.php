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
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->unsignedBigInteger('starting_bid');
            $table->unsignedBigInteger('min_increment');
            $table->unsignedBigInteger('current_high_bid')->nullable();
            $table->foreignId('highest_bidder_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('start_time')->index();
            $table->dateTime('end_time')->index();
            $table->string('status')->default('upcoming')->index();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};

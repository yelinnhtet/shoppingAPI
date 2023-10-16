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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('currency_id');
            $table->foreign('currency_id')
                    ->references('id')->on('currencies')
                    ->onDelete('cascade');

            $table->string('name')->nullable();
            $table->string('phonecode')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('country_img')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
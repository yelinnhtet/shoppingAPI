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
        Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('contact')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');

            $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')
                    ->references('id')->on('countries')
                    ->onDelete('cascade');

            $table->unsignedBigInteger('state_id');
            $table->foreign('state_id')
                    ->references('id')->on('states')
                    ->onDelete('cascade');

            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')
                    ->references('id')->on('cities')
                    ->onDelete('cascade');

            $table->string('phone')->nullable();
            // $table->enum('is_default',['1','0']);
            $table->enum('is_default', ['1', '0'])->default('0');
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_addresses');
    }
};

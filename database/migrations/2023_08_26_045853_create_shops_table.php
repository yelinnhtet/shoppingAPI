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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            $table->foreignId('agent_user_id');
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
            $table->string('name')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_photo')->nullable();
            $table->string('shopkeeper_nrc')->nullable();
            $table->string('nrc')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};

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
        Schema::create('good_specs', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->longText('value')->nullable();
            $table->unsignedBigInteger('good_id');
            $table->foreign('good_id')
                    ->references('id')->on('goods')
                    ->onDelete('cascade');
                    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_specs');
    }
};

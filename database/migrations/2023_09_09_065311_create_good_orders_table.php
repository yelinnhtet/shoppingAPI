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
        Schema::create('good_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')
                    ->references('id')->on('orders')
                    ->onDelete('cascade');
            $table->unsignedBigInteger('good_id');
            $table->foreign('good_id')
                    ->references('id')->on('goods')
                    ->onDelete('cascade');
            $table->unsignedBigInteger('good_para_id');
            $table->foreign('good_para_id')
                    ->references('id')->on('good_paras')
                    ->onDelete('cascade');
            $table->decimal('price');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_orders');
    }
};

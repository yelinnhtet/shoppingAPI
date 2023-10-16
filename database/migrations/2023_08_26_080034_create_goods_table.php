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
        Schema::create('goods', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('photo')->nullable();
            
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')
                    ->references('id')->on('shops')
                    ->onDelete('cascade');

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
                    ->references('id')->on('categories')
                    ->onDelete('cascade');

            $table->float('price')->nullable();
            $table->float('discount')->nullable();
            $table->string('description')->nullable();

            // $table->unsignedBigInteger('good_spec_id');
            // $table->foreign('good_spec_id')
            //         ->references('id')->on('good_specs')
            //         ->onDelete('cascade');
            
            // $table->unsignedBigInteger('good_para_id');
            // $table->foreign('good_para_id')
            //         ->references('id')->on('good_paras')
            //         ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};

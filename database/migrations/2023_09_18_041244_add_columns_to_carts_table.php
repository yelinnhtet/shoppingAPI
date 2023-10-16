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
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('good_paras_id')->after('good_id');
            $table->foreign('good_paras_id')
                    ->references('id')->on('good_paras')
                    ->onDelete('cascade');
            $table->unsignedBigInteger('good_specs_id')->after('good_paras_id');
            $table->foreign('good_specs_id')
                    ->references('id')->on('good_specs')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            //
        });
    }
};

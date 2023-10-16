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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('good_id')->after('order_number');
            $table->string('good_paras_id')->after('good_id');
            $table->string('good_specs_id')->after('good_paras_id');
            $table->string('quantity')->after('user_id');
            $table->string('totalPrice')->after('quantity');
            $table->enum('status',[1,0])->default(0)->after('totalPrice')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};

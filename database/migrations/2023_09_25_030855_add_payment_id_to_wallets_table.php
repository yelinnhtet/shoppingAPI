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
        Schema::table('wallets', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id')->after('user_id');
            $table->foreign('payment_id')
                    ->references('id')->on('payments')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            //
        });
    }
};

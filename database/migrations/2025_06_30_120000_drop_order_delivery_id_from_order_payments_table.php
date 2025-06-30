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
        Schema::table('order_payments', function (Blueprint $table) {
            if (Schema::hasColumn('order_payments', 'order_delivery_id')) {
                $table->dropForeign(['order_delivery_id']);
                $table->dropColumn('order_delivery_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('order_delivery_id')->nullable();
            // You may need to re-add the foreign key if needed
            // $table->foreign('order_delivery_id')->references('id')->on('order_deliveries')->onDelete('set null');
        });
    }
};

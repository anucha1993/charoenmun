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
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'delivery_id')) {
                $table->dropForeign(['delivery_id']);
                $table->dropColumn('delivery_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->foreign('delivery_id')->references('id')->on('order_deliveries')->onDelete('set null');
        });
    }
};

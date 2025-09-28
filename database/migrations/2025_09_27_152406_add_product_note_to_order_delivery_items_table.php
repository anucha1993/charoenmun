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
        Schema::table('order_delivery_items', function (Blueprint $table) {
            $table->text('product_note')->nullable()->after('product_calculation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_delivery_items', function (Blueprint $table) {
            $table->dropColumn('product_note');
        });
    }
};

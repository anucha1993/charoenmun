<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('added_reason')->nullable()->after('product_note'); // 'quotation', 'claim', 'customer_request'
            $table->text('added_note')->nullable()->after('added_reason');
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['added_reason', 'added_note']);
        });
    }
};

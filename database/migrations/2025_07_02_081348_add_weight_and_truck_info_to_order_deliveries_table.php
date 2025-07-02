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
        Schema::table('order_deliveries', function (Blueprint $table) {
            $table->decimal('total_weight_kg', 10, 2)->default(0)->after('order_delivery_note')->comment('น้ำหนักรวม (กิโลกรัม)');
            $table->enum('recommended_truck_type', ['six_wheel_large', 'six_wheel_small', 'six_wheel_medium', 'ten_wheel'])->nullable()->after('total_weight_kg')->comment('ประเภทรถที่แนะนำ');
            $table->enum('selected_truck_type', ['six_wheel_large', 'six_wheel_small', 'six_wheel_medium', 'ten_wheel'])->nullable()->after('recommended_truck_type')->comment('ประเภทรถที่เลือกใช้');
            $table->text('truck_note')->nullable()->after('selected_truck_type')->comment('หมายเหตุเกี่ยวกับรถขนส่ง');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'total_weight_kg',
                'recommended_truck_type', 
                'selected_truck_type',
                'truck_note'
            ]);
        });
    }
};

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
        Schema::table('delivery_address', function (Blueprint $table) {
            // เพิ่มฟิลด์ที่อยู่แบบ textarea
            $table->text('delivery_address')->nullable()->after('delivery_phone');
            
            // ลบฟิลด์เก่าที่ไม่ใช้แล้ว (ในอนาคต)
            // $table->dropColumn(['delivery_province', 'delivery_amphur', 'delivery_district', 'delivery_zipcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_address', function (Blueprint $table) {
            // คืนค่าฟิลด์เก่า
            // $table->string('delivery_province')->nullable();
            // $table->string('delivery_amphur')->nullable();
            // $table->string('delivery_district')->nullable();
            // $table->string('delivery_zipcode')->nullable();
            
            // ลบฟิลด์ที่อยู่ใหม่
            $table->dropColumn('delivery_address');
        });
    }
};

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
        Schema::create('delivery_prints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_delivery_id')->constrained('order_deliveries')->onDelete('cascade');
            $table->string('printed_by')->nullable(); // ชื่อผู้พิมพ์ (อาจเป็นชื่อผู้ใช้หรือชื่อที่กรอกเข้ามา)
            $table->integer('print_count')->default(1); // ครั้งที่พิมพ์ (เริ่มจาก 1)
            $table->timestamp('printed_at')->useCurrent(); // วันเวลาที่พิมพ์
            $table->boolean('is_complete_delivery')->default(false); // สถานะว่าส่งครบหรือไม่
            $table->string('approved_code')->nullable(); // รหัสยืนยันการพิมพ์ (กรณีพิมพ์เกิน 3 ครั้ง)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_prints');
    }
};

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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
        
            // 🔗 ความสัมพันธ์
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('order_delivery_id')->constrained('order_deliveries')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('set null')->nullable(); // ⬅️ ผู้แจ้งชำระ
        
            // 🖼️ สลิป
            $table->string('slip_path');
            $table->decimal('amount', 10, 2);
        
            // 🏦 รายละเอียดจาก Slip2Go
            $table->string('reference_id')->nullable();
            $table->string('trans_ref')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->timestamp('transfer_at')->nullable();
        
            // 🔄 สถานะการชำระ
            $table->enum('status', ['รอยืนยันยอด', 'ชำระถูกต้อง', 'ปฏิเสธ'])->default('รอยืนยันยอด');
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};

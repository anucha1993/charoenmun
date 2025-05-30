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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_id')->unique();          // QTYMM-#### แบบที่คุณต้องการ
            $table->string('quotation_number')->unique();          // QTYMM-#### แบบที่คุณต้องการ
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('delivery_address_id')->nullable()->constrained('delivery_addresses');
            $table->date('quote_date')->default(now());
            $table->text('note')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('vat', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->boolean('enable_vat')->default(false);
            $table->boolean('vat_included')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id'); // 1.
            $table->string('product_code')->unique(); // 2.
            $table->string('product_name'); // 3.
            $table->decimal('product_weight', 8, 2); // 4.
            $table->decimal('product_price', 10, 2); // 5.
            $table->string('product_type'); // 6.
            $table->string('product_unit'); // 7.
            $table->text('product_note')->nullable(); // 8.
            $table->boolean('product_status')->default(1); // 9. 1=Active,0=Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

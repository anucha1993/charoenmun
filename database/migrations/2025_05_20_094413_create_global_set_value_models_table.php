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
        Schema::create('global_set_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('global_set_id')->constrained()->onDelete('cascade');
            $table->string('value'); // เช่น Wechat
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            $table->integer('sort_order')->default(0); // ใช้จัดลำดับการแสดง
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_set_values');
    }
};

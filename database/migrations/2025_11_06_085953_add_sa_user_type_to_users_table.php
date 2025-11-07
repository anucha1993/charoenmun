<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // อัปเดต enum สำหรับ type column เพื่อเพิ่ม SA
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('admin', 'member', 'SA') DEFAULT 'member'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // กลับไปเป็นเดิม
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('admin', 'member') DEFAULT 'member'");
    }
};

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
       Schema::create('customers', function (Blueprint $t) {
            $t->id();
            $t->string('customer_code')->unique();   // CUS-202500001
            $t->string('customer_name');
            $t->string('customer_type')->nullable();
            $t->string('customer_level')->nullable();
            $t->string('customer_taxid')->nullable();
            $t->string('customer_contract_name')->nullable();
            $t->string('customer_phone')->nullable();
            $t->string('customer_email')->nullable();
            $t->string('customer_idline')->nullable();
            $t->text('customer_address')->nullable();
            $t->string('customer_province', 4)->nullable();
            $t->string('customer_amphur', 4)->nullable();
            $t->string('customer_district', 6)->nullable();
            $t->string('customer_zipcode', 5)->nullable();
            $t->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

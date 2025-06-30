<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quotations\QuotationModel;
use App\Models\Quotations\QuotationItemModel;
use App\Models\customers\customerModel;
use App\Models\products\ProductModel;
use App\Models\User;
use Carbon\Carbon;

class QuotationTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ดึงข้อมูลที่จำเป็น
        $customers = customerModel::limit(2)->get();
        $products = ProductModel::limit(3)->get();
        $user = User::first();

        if ($customers->isEmpty() || $products->isEmpty() || !$user) {
            $this->command->error('ไม่พบข้อมูลลูกค้า สินค้า หรือผู้ใช้ในระบบ');
            return;
        }

        // สร้างใบเสนอราคาที่ 1
        $quotation1 = QuotationModel::create([
            'quote_number' => 'QT' . now()->format('ym') . '0001',
            'customer_id' => $customers[0]->id,
            'delivery_address_id' => null,
            'quote_date' => now()->toDateString(),
            'quote_note' => 'ใบเสนอราคาตัวอย่างที่ 1 - สำหรับทดสอบระบบ CRUD',
            'quote_discount' => 500.00,
            'quote_subtotal' => 15000.00,
            'quote_vat' => 1050.00,
            'quote_grand_total' => 16550.00,
            'quote_enable_vat' => true,
            'quote_vat_included' => false,
            'quote_status' => 'wait',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // สร้างรายการสินค้าสำหรับใบเสนอราคาที่ 1
        QuotationItemModel::create([
            'quotation_id' => $quotation1->id,
            'product_id' => $products[0]->product_id,
            'product_name' => $products[0]->product_name,
            'product_type' => $products[0]->productType->value ?? 'concrete',
            'product_unit' => $products[0]->productUnit->value ?? 'ชิ้น',
            'product_detail' => $products[0]->product_size ?? '',
            'product_length' => '6',
            'product_weight' => $products[0]->product_weight ?? 0,
            'product_calculation' => $products[0]->product_calculation ?? 1,
            'product_note' => 'รายการทดสอบที่ 1',
            'quantity' => 10,
            'unit_price' => 750.00,
            'total' => 7500.00,
            'product_vat' => 1,
        ]);

        QuotationItemModel::create([
            'quotation_id' => $quotation1->id,
            'product_id' => $products[1]->product_id,
            'product_name' => $products[1]->product_name,
            'product_type' => $products[1]->productType->value ?? 'concrete',
            'product_unit' => $products[1]->productUnit->value ?? 'ชิ้น',
            'product_detail' => $products[1]->product_size ?? '',
            'product_length' => '8',
            'product_weight' => $products[1]->product_weight ?? 0,
            'product_calculation' => $products[1]->product_calculation ?? 1,
            'product_note' => 'รายการทดสอบที่ 2',
            'quantity' => 15,
            'unit_price' => 500.00,
            'total' => 7500.00,
            'product_vat' => 1,
        ]);

        // สร้างใบเสนอราคาที่ 2
        $quotation2 = QuotationModel::create([
            'quote_number' => 'QT' . now()->format('ym') . '0002',
            'customer_id' => $customers->count() > 1 ? $customers[1]->id : $customers[0]->id,
            'delivery_address_id' => null,
            'quote_date' => now()->subDays(1)->toDateString(),
            'quote_note' => 'ใบเสนอราคาตัวอย่างที่ 2 - ทดสอบ VAT แบบรวมในราคา',
            'quote_discount' => 0.00,
            'quote_subtotal' => 18691.59,
            'quote_vat' => 1308.41,
            'quote_grand_total' => 20000.00,
            'quote_enable_vat' => true,
            'quote_vat_included' => true,
            'quote_status' => 'wait',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // สร้างรายการสินค้าสำหรับใบเสนอราคาที่ 2
        QuotationItemModel::create([
            'quotation_id' => $quotation2->id,
            'product_id' => $products[2]->product_id ?? $products[0]->product_id,
            'product_name' => $products[2]->product_name ?? $products[0]->product_name,
            'product_type' => $products[2]->productType->value ?? 'concrete',
            'product_unit' => $products[2]->productUnit->value ?? 'ชิ้น',
            'product_detail' => $products[2]->product_size ?? '',
            'product_length' => '10',
            'product_weight' => $products[2]->product_weight ?? 0,
            'product_calculation' => $products[2]->product_calculation ?? 1,
            'product_note' => 'รายการทดสอบใบเสนอราคาที่ 2',
            'quantity' => 20,
            'unit_price' => 1000.00,
            'total' => 20000.00,
            'product_vat' => 1,
        ]);

        $this->command->info('✅ สร้างข้อมูลใบเสนอราคาตัวอย่าง 2 ใบสำเร็จ!');
        $this->command->info("📄 ใบเสนอราคาที่ 1: {$quotation1->quote_number} - ลูกค้า: {$customers[0]->customer_name}");
        $this->command->info("📄 ใบเสนอราคาที่ 2: {$quotation2->quote_number} - ลูกค้า: " . ($customers->count() > 1 ? $customers[1]->customer_name : $customers[0]->customer_name));
    }
}

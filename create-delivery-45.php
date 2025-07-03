<?php

// แสดง error ทั้งหมด
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ตั้งค่า Output
echo "=================================================================\n";
echo "🚚 สร้างรอบจัดส่งใหม่สำหรับ Order ID 45\n";
echo "=================================================================\n\n";

try {
    // โหลด Autoload
    require_once __DIR__ . '/vendor/autoload.php';

    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // Import Facades
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;

    // ตั้งค่า Auth User (เพื่อใช้ในการบันทึกว่าใครเป็นคนสร้าง)
    Auth::loginUsingId(1); // ล็อกอินเป็น Admin (user_id = 1)

    // 1. ดึงข้อมูล Order
    echo "1. ดึงข้อมูล Order ID 45...\n";
    $order = \App\Models\Orders\OrderModel::with(['customer', 'deliveryAddress', 'items', 'deliveries'])->find(45);

    if (!$order) {
        die("❌ ไม่พบ Order ID 45\n");
    }

    echo "✅ พบข้อมูล Order\n";
    echo " - Order Number: {$order->order_number}\n";
    echo " - ลูกค้า: {$order->customer->customer_name}\n";
    echo " - จำนวนสินค้า: {$order->items->count()} รายการ\n";
    echo " - จำนวนรอบจัดส่งที่มีอยู่: {$order->deliveries->count()} รอบ\n\n";

    // 2. สร้างเลข Delivery Number
    echo "2. สร้างเลข Delivery Number...\n";
    $prefix = 'ODL' . now()->format('ym');
    $last = \App\Models\Orders\OrderDeliverysModel::where('order_delivery_number', 'like', $prefix . '%')
        ->orderBy('order_delivery_number', 'desc')
        ->value('order_delivery_number');

    $next = $last ? ((int) substr($last, -4)) + 1 : 1;
    $deliveryNumber = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

    echo "✅ เลขที่การจัดส่งใหม่: {$deliveryNumber}\n\n";

    // 3. เริ่มต้น Transaction
    echo "3. เริ่มต้นการบันทึกข้อมูล (Transaction)...\n";
    DB::beginTransaction();

    try {
        // 4. สร้างรอบจัดส่งใหม่
        echo "4. สร้างรอบจัดส่งใหม่...\n";
        
        // ใช้วิธี create แทน new เพื่อใช้ mass assignment
        $delivery = \App\Models\Orders\OrderDeliverysModel::create([
            'order_id' => $order->id,
            'order_delivery_number' => $deliveryNumber,
            'order_delivery_date' => now(),
            'order_delivery_status' => 'pending',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'delivery_address_id' => $order->deliveryAddress ? $order->deliveryAddress->id : 1,
            'order_delivery_enable_vat' => 1,
            'order_delivery_vat_included' => 0,
            'order_delivery_note' => 'สร้างจากสคริปต์ทดสอบ ' . date('Y-m-d H:i:s'),
        ]);
        
        echo "✅ สร้างรอบจัดส่งสำเร็จ (ID: {$delivery->id})\n\n";
        
        // 5. เพิ่มสินค้าที่ยังไม่ได้จัดส่งเข้าไปในรอบจัดส่ง
        echo "5. เพิ่มสินค้าในรอบจัดส่ง...\n";
        
        // ดึงข้อมูลว่าแต่ละ item ถูกจัดส่งไปแล้วเท่าไหร่
        $deliveredQtyMap = \App\Models\Orders\OrderDeliveryItems::query()
            ->join('order_items', 'order_delivery_items.order_item_id', '=', 'order_items.id')
            ->where('order_items.order_id', $order->id)
            ->select('order_items.id as order_item_id', DB::raw('SUM(order_delivery_items.quantity) as delivered'))
            ->groupBy('order_items.id')
            ->pluck('delivered', 'order_item_id')
            ->toArray();
        
        $totalWeight = 0;
        
        foreach ($order->items as $item) {
            $ordered = $item->quantity;
            $delivered = $deliveredQtyMap[$item->id] ?? 0;
            $remaining = $ordered - $delivered;
            
            if ($remaining > 0) {
                $deliveryItem = new \App\Models\Orders\OrderDeliveryItems();
                $deliveryItem->order_delivery_id = $delivery->id;
                $deliveryItem->order_item_id = $item->id;
                $deliveryItem->quantity = $remaining; // จัดส่งทั้งหมดที่เหลือ
                $deliveryItem->save();
                
                // คำนวณน้ำหนัก
                $productWeight = $item->product->product_weight ?? 0;
                $weight = $productWeight * $remaining;
                $totalWeight += $weight;
                
                echo " - {$item->product_name}: จัดส่ง {$remaining} หน่วย (น้ำหนัก: " . number_format($weight, 2) . " กก.)\n";
            }
        }
        
        // 6. อัปเดตน้ำหนักและแนะนำประเภทรถ
        echo "\n6. อัปเดตน้ำหนักและแนะนำประเภทรถ...\n";
        
        $delivery->total_weight_kg = $totalWeight;
        
        // แนะนำประเภทรถตามน้ำหนัก
        $recommendedTruck = \App\Enums\TruckType::getRecommendedTruck($totalWeight);
        $delivery->recommended_truck_type = $recommendedTruck;
        $delivery->selected_truck_type = $recommendedTruck; // ตั้งค่าเริ่มต้นให้ใช้รถที่แนะนำ
        
        $delivery->save();
        
        echo "✅ น้ำหนักรวม: " . number_format($totalWeight, 2) . " กก.\n";
        echo "✅ รถที่แนะนำ: {$recommendedTruck->name}\n\n";
        
        // 7. Commit Transaction
        DB::commit();
        echo "✅ บันทึกข้อมูลทั้งหมดเรียบร้อย\n";
        
        // 8. แสดงข้อมูลสรุป
        echo "\n=================================================================\n";
        echo "📋 สรุปข้อมูลการจัดส่งที่สร้าง\n";
        echo "=================================================================\n";
        echo "เลขที่รอบจัดส่ง: {$deliveryNumber}\n";
        echo "วันที่จัดส่ง: " . now()->format('d/m/Y') . "\n";
        echo "ลูกค้า: {$order->customer->customer_name}\n";
        echo "น้ำหนักรวม: " . number_format($totalWeight, 2) . " กก. (" . number_format($totalWeight / 1000, 2) . " ตัน)\n";
        echo "รถที่แนะนำ: {$recommendedTruck->name}\n";
        echo "สถานะ: pending (รอจัดส่ง)\n\n";
    } catch (\Exception $e) {
        // มีข้อผิดพลาด, Rollback Transaction
        DB::rollBack();
        echo "❌ เกิดข้อผิดพลาด: {$e->getMessage()}\n";
        echo "ที่ไฟล์: {$e->getFile()} บรรทัด: {$e->getLine()}\n";
        echo "ทำการยกเลิกการบันทึกข้อมูลทั้งหมด (Rollback)\n";
        throw $e;
    }

    // จบการทำงาน
    echo "🏁 เสร็จสิ้น สามารถตรวจสอบข้อมูลได้ในระบบ\n";
    echo "=================================================================\n";

} catch (\Throwable $e) {
    echo "❌❌❌ เกิดข้อผิดพลาดร้ายแรง: " . $e->getMessage() . "\n";
    echo "ที่ไฟล์: " . $e->getFile() . " บรรทัด: " . $e->getLine() . "\n";
    echo "Stack Trace: \n" . $e->getTraceAsString() . "\n";
}

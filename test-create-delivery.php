<?php

/**
 * สคริปต์ทดสอบการสร้างรอบจัดส่งใหม่สำหรับ Order ID 46
 */

// โหลด Autoload
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import DB Facade
use Illuminate\Support\Facades\DB;

// เริ่มการทำงาน
echo "=================================================================\n";
echo "🚚 ทดสอบการสร้างรอบจัดส่ง (Delivery) สำหรับ Order ID 46\n";
echo "=================================================================\n\n";

// ค้นหา Order
$order = \App\Models\Orders\OrderModel::with(['customer', 'deliveryAddress', 'items'])->find(46);

if (!$order) {
    die("❌ ไม่พบ Order ID 46\n");
}

echo "ข้อมูล Order: #{$order->order_number} | {$order->customer->customer_name}\n";
echo "จำนวนสินค้า: {$order->items->count()} รายการ\n\n";

// 1. สร้าง Delivery ใหม่
echo "1. สร้างรอบจัดส่งใหม่...\n";

try {
    $prefix = 'ODL' . now()->format('ym');
    $last = \App\Models\Orders\OrderDeliverysModel::where('order_delivery_number', 'like', $prefix . '%')
        ->orderBy('order_delivery_number', 'desc')
        ->value('order_delivery_number');
    
    $next = $last ? ((int) substr($last, -4)) + 1 : 1;
    $deliveryNumber = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    
    // สร้าง Delivery
    $delivery = new \App\Models\Orders\OrderDeliverysModel();
    $delivery->order_id = $order->id;
    $delivery->order_delivery_number = $deliveryNumber;
    $delivery->delivery_date = now();
    $delivery->delivery_status = 'pending';
    $delivery->created_by = 1; // Admin user
    
    // ใช้ที่อยู่จัดส่งจาก Order
    if ($order->deliveryAddress) {
        $delivery->delivery_address = $order->deliveryAddress->delivery_address;
        $delivery->delivery_contact_name = $order->deliveryAddress->delivery_contact_name;
        $delivery->delivery_phone = $order->deliveryAddress->delivery_phone;
    } else {
        $delivery->delivery_address = $order->customer->customer_address;
        $delivery->delivery_contact_name = $order->customer->customer_name;
        $delivery->delivery_phone = $order->customer->customer_phone;
    }
    
    $delivery->save();
    
    echo "✅ สร้างรอบจัดส่งใหม่สำเร็จ: {$deliveryNumber}\n";
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาดในการสร้างรอบจัดส่ง: {$e->getMessage()}\n";
    die();
}

// 2. เพิ่มสินค้าเข้าไปในรอบจัดส่ง (ทั้งหมด 50% ของแต่ละรายการ)
echo "\n2. เพิ่มสินค้าเข้าไปในรอบจัดส่ง...\n";

try {
    foreach ($order->items as $item) {
        // กำหนดให้จัดส่ง 50% ของแต่ละรายการสินค้า
        $deliveryQty = ceil($item->quantity / 2);
        
        $deliveryItem = new \App\Models\Orders\OrderDeliveryItems();
        $deliveryItem->order_delivery_id = $delivery->id;
        $deliveryItem->order_item_id = $item->id;
        $deliveryItem->quantity = $deliveryQty;
        $deliveryItem->created_by = 1; // Admin user
        $deliveryItem->save();
        
        echo "- เพิ่มสินค้า: {$item->product_name} จำนวน {$deliveryQty}/{$item->quantity} หน่วย\n";
    }
    
    echo "✅ เพิ่มสินค้าในรอบจัดส่งสำเร็จ\n";
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาดในการเพิ่มสินค้า: {$e->getMessage()}\n";
}

// 3. คำนวณน้ำหนักและแนะนำรถ
echo "\n3. คำนวณน้ำหนักและแนะนำประเภทรถ...\n";

try {
    // โหลด delivery items พร้อม product
    $delivery->load('deliveryItems.orderItem.product');
    
    // คำนวณน้ำหนักทั้งหมด
    $totalWeight = 0;
    foreach ($delivery->deliveryItems as $item) {
        $productWeight = $item->orderItem->product->product_weight ?? 0;
        $weight = $productWeight * $item->quantity;
        $totalWeight += $weight;
    }
    
    $delivery->total_weight_kg = $totalWeight;
    
    // แนะนำประเภทรถตามน้ำหนัก
    $recommendedTruck = \App\Enums\TruckType::getRecommendedTruck($totalWeight);
    $delivery->recommended_truck_type = $recommendedTruck;
    $delivery->selected_truck_type = $recommendedTruck;
    
    $delivery->save();
    
    echo "น้ำหนักรวม: " . number_format($totalWeight, 2) . " กก.\n";
    echo "รถที่แนะนำ: {$recommendedTruck->name}\n";
    echo "✅ อัปเดตน้ำหนักและประเภทรถสำเร็จ\n";
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาดในการคำนวณน้ำหนัก: {$e->getMessage()}\n";
}

// 4. ทดสอบการเปลี่ยนสถานะเป็น 'delivered'
echo "\n4. ทดสอบการเปลี่ยนสถานะเป็น 'delivered'...\n";

try {
    $delivery->delivery_status = 'delivered';
    $delivery->updated_by = 1; // Admin user
    $delivery->save();
    
    echo "✅ เปลี่ยนสถานะเป็น 'delivered' สำเร็จ\n";
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาดในการเปลี่ยนสถานะ: {$e->getMessage()}\n";
}

// 5. ตรวจสอบ deliveredQtyMap หลังจากมีการจัดส่ง
echo "\n5. ตรวจสอบจำนวนสินค้าที่จัดส่งแล้ว...\n";

try {
    $deliveredQtyMap = \App\Models\Orders\OrderDeliveryItems::query()
        ->join('order_items', 'order_delivery_items.order_item_id', '=', 'order_items.id')
        ->where('order_items.order_id', $order->id)
        ->select('order_items.id as order_item_id', DB::raw('SUM(order_delivery_items.quantity) as delivered'))
        ->groupBy('order_items.id')
        ->pluck('delivered', 'order_item_id')
        ->toArray();
    
    echo "รายการสินค้าและจำนวนที่จัดส่งแล้ว:\n";
    
    foreach ($order->items as $item) {
        $ordered = $item->quantity;
        $delivered = $deliveredQtyMap[$item->id] ?? 0;
        $remaining = $ordered - $delivered;
        
        echo "- {$item->product_name}: สั่ง {$ordered} จัดส่งแล้ว {$delivered} คงเหลือ {$remaining}\n";
    }
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาดในการตรวจสอบจำนวนสินค้า: {$e->getMessage()}\n";
}

// 6. ทดสอบ Livewire Component
echo "\n6. ทดสอบ OrderShow Component หลังจากสร้างรอบจัดส่ง...\n";

try {
    // โหลด order ใหม่พร้อม relationships
    $order = \App\Models\Orders\OrderModel::with([
        'customer', 
        'deliveryAddress', 
        'items', 
        'deliveries.deliveryItems.orderItem', 
        'payments'
    ])->find(46);
    
    $component = new \App\Livewire\Orders\OrderShow();
    $component->mount($order);
    
    $weightSummary = $component->getDeliveryWeightSummary();
    $transportSummary = $component->getOrderTransportSummary();
    
    echo "จำนวนการจัดส่งทั้งหมด: {$weightSummary['total_deliveries']} รอบ\n";
    echo "น้ำหนักรวมทั้งหมด: " . number_format($weightSummary['total_weight'], 2) . " กก.\n";
    echo "น้ำหนักที่จัดส่งแล้ว: " . number_format($weightSummary['completed_weight'], 2) . " กก.\n";
    echo "น้ำหนักที่รอจัดส่ง: " . number_format($weightSummary['pending_weight'], 2) . " กก.\n";
    
    echo "\nสรุปการขนส่ง:\n";
    echo "น้ำหนักรวมของออร์เดอร์: " . number_format($transportSummary['total_order_weight_kg'], 2) . " กก. (" . number_format($transportSummary['total_order_weight_ton'], 2) . " ตัน)\n";
    echo "น้ำหนักที่จัดส่งแล้ว: " . number_format($transportSummary['total_delivery_weight_kg'], 2) . " กก. (" . number_format($transportSummary['total_delivery_weight_ton'], 2) . " ตัน)\n";
    echo "จำนวนรอบการจัดส่ง: {$transportSummary['deliveries_count']} รอบ\n";
    
    echo "✅ ทดสอบ Component หลังจากสร้างรอบจัดส่งสำเร็จ\n";
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาดในการทดสอบ Component: {$e->getMessage()}\n";
}

// จบการทดสอบ
echo "\n=================================================================\n";
echo "🏁 จบการทดสอบการสร้างรอบจัดส่ง | " . date('Y-m-d H:i:s') . "\n";
echo "=================================================================\n";

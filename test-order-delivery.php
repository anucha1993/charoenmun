<?php

/**
 * สคริปต์ทดสอบระบบ Order และ Delivery เต็มรูปแบบ
 * ใช้ทดสอบกับ Order ID 46
 */

// โหลด Autoload
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ตั้งค่า Output
echo "=================================================================\n";
echo "🧪 ทดสอบระบบ Order และ Delivery โดยใช้ Order ID 45\n";
echo "=================================================================\n\n";

// ฟังก์ชั่นแสดงผลข้อมูล
function printSection($title) {
    echo "\n------------------------------------------------------------------\n";
    echo "🔍 $title\n";
    echo "------------------------------------------------------------------\n";
}

function testFunctionality($title, $callback) {
    echo "\n👉 ทดสอบ: $title\n";
    try {
        $result = $callback();
        echo "✅ ทำงานสำเร็จ\n";
        return $result;
    } catch (\Exception $e) {
        echo "❌ พบข้อผิดพลาด: " . $e->getMessage() . "\n";
        echo "ที่ไฟล์: " . $e->getFile() . " บรรทัด: " . $e->getLine() . "\n";
        return null;
    }
}

// 1. ดึงข้อมูล Order
printSection("1. ดึงข้อมูล Order");
$order = testFunctionality("ค้นหา Order ID 45", function() {
    $order = \App\Models\Orders\OrderModel::find(45);
    if (!$order) {
        throw new \Exception("ไม่พบออร์เดอร์ ID 45");
    }
    
    echo "Order Number: {$order->order_number}\n";
    echo "วันที่สั่งซื้อ: " . ($order->order_date ? $order->order_date->format('d/m/Y') : 'ไม่มีข้อมูล') . "\n";
    echo "สถานะ: {$order->order_status}\n";
    echo "ยอดรวม: {$order->order_grand_total}\n";
    
    return $order;
});

if (!$order) {
    echo "❌ ไม่สามารถดำเนินการต่อได้เนื่องจากไม่พบข้อมูล Order\n";
    exit(1);
}

// 2. ทดสอบโหลดข้อมูล Order พร้อม Relationships
printSection("2. ทดสอบโหลดข้อมูล Order พร้อม Relationships");
testFunctionality("โหลดข้อมูล Customer, Items, Deliveries", function() use ($order) {
    $order->load(['customer', 'items', 'deliveries.deliveryItems', 'payments']);
    
    echo "ชื่อลูกค้า: " . ($order->customer ? $order->customer->customer_name : 'ไม่มีข้อมูล') . "\n";
    echo "จำนวนสินค้า: " . $order->items->count() . " รายการ\n";
    echo "จำนวนการจัดส่ง: " . $order->deliveries->count() . " รอบ\n";
    echo "จำนวนการชำระเงิน: " . $order->payments->count() . " รายการ\n";
    
    return true;
});

// 3. สร้าง Livewire Component เพื่อทดสอบฟังก์ชัน
printSection("3. ทดสอบฟังก์ชันใน OrderShow Component");
$component = testFunctionality("สร้าง OrderShow Component", function() use ($order) {
    $component = new \App\Livewire\Orders\OrderShow();
    $component->mount($order); // เรียก mount() เพื่อเริ่มต้นค่า
    return $component;
});

if (!$component) {
    echo "❌ ไม่สามารถสร้าง Livewire Component ได้\n";
    exit(1);
}

// 4. ทดสอบการคำนวณยอดรวม
printSection("4. ทดสอบการคำนวณยอดรวม");
testFunctionality("calculateTotals()", function() use ($component) {
    $component->calculateTotals();
    
    echo "ยอดรวมก่อนหักส่วนลด: " . number_format($component->order_subtotal_before_discount, 2) . "\n";
    echo "ส่วนลด: " . number_format($component->order_discount, 2) . "\n";
    echo "ยอดหลังหักส่วนลด: " . number_format($component->order_subtotal, 2) . "\n";
    echo "ภาษีมูลค่าเพิ่ม: " . number_format($component->order_vat, 2) . "\n";
    echo "ยอดรวมสุทธิ: " . number_format($component->order_grand_total, 2) . "\n";
    
    return true;
});

// 5. ทดสอบการคำนวณน้ำหนักและการแนะนำประเภทรถ
printSection("5. ทดสอบการคำนวณน้ำหนักและการแนะนำประเภทรถ");
testFunctionality("calculateOrderWeight()", function() use ($component) {
    $weight = $component->calculateOrderWeight();
    echo "น้ำหนักรวมของออเดอร์: " . number_format($weight, 2) . " กิโลกรัม\n";
    return $weight;
});

testFunctionality("recommendTruckForOrder()", function() use ($component) {
    $truck = $component->recommendTruckForOrder();
    echo "รถที่แนะนำสำหรับออเดอร์นี้: " . ($truck ? $truck->name : 'ไม่มีข้อมูล') . "\n";
    return $truck;
});

// 6. ทดสอบการดึงข้อมูลสรุปการจัดส่ง
printSection("6. ทดสอบการดึงข้อมูลสรุปการจัดส่ง");
testFunctionality("getDeliveryWeightSummary()", function() use ($component) {
    $summary = $component->getDeliveryWeightSummary();
    
    echo "จำนวนการจัดส่งทั้งหมด: " . $summary['total_deliveries'] . " รอบ\n";
    echo "น้ำหนักรวมทั้งหมด: " . number_format($summary['total_weight'], 2) . " กิโลกรัม\n";
    echo "น้ำหนักที่จัดส่งแล้ว: " . number_format($summary['completed_weight'], 2) . " กิโลกรัม\n";
    echo "น้ำหนักที่รอจัดส่ง: " . number_format($summary['pending_weight'], 2) . " กิโลกรัม\n";
    
    echo "ประเภทรถที่ใช้: \n";
    foreach ($summary['truck_types'] as $typeName => $count) {
        echo " - " . ($typeName ?: 'ไม่ระบุ') . ": " . $count . " รอบ\n";
    }
    
    return $summary;
});

// 7. ทดสอบการดึงข้อมูลสรุปการขนส่ง
printSection("7. ทดสอบการดึงข้อมูลสรุปการขนส่ง");
testFunctionality("getOrderTransportSummary()", function() use ($component) {
    $summary = $component->getOrderTransportSummary();
    
    echo "น้ำหนักรวมของออร์เดอร์: " . number_format($summary['total_order_weight_kg'], 2) . " กก. (" . number_format($summary['total_order_weight_ton'], 2) . " ตัน)\n";
    echo "น้ำหนักที่จัดส่งแล้ว: " . number_format($summary['total_delivery_weight_kg'], 2) . " กก. (" . number_format($summary['total_delivery_weight_ton'], 2) . " ตัน)\n";
    echo "จำนวนรอบการจัดส่ง: " . $summary['deliveries_count'] . " รอบ\n";
    echo "ประเภทรถที่ใช้: " . implode(", ", $summary['truck_types']) . "\n";
    echo "จำนวนรอบที่น้ำหนักเกิน: " . $summary['overweight_deliveries'] . " รอบ\n";
    echo "จำนวนเที่ยวรถที่ต้องใช้: " . $summary['total_trips_required'] . " เที่ยว\n";
    echo "รถที่แนะนำสำหรับทั้งออร์เดอร์: " . ($summary['recommended_truck_for_full_order'] ? $summary['recommended_truck_for_full_order']->name : 'ไม่มีข้อมูล') . "\n";
    
    return $summary;
});

// 8. ทดสอบการสร้างเลข Delivery Number
printSection("8. ทดสอบการสร้างเลข Delivery Number");
testFunctionality("generateOrderDeliveryNumber()", function() use ($component) {
    // ใช้ Reflection เพื่อเข้าถึง private method
    $reflection = new ReflectionClass($component);
    $method = $reflection->getMethod('generateOrderDeliveryNumber');
    $method->setAccessible(true);
    
    $deliveryNumber = $method->invoke($component);
    echo "เลขที่รอบจัดส่งใหม่: " . $deliveryNumber . "\n";
    
    return $deliveryNumber;
});

// 9. ตรวจสอบสถานะรวมของออร์เดอร์
printSection("9. ตรวจสอบสถานะรวมของออร์เดอร์");
testFunctionality("ตรวจสอบสถานะการจัดส่ง", function() use ($order) {
    $deliveryStatuses = $order->deliveries->pluck('delivery_status')->toArray();
    $allDelivered = count($deliveryStatuses) > 0 && count(array_filter($deliveryStatuses, fn($s) => $s !== 'delivered')) === 0;
    
    echo "สถานะการจัดส่งทั้งหมด: " . implode(", ", $deliveryStatuses) . "\n";
    echo "จัดส่งครบทุกรอบ: " . ($allDelivered ? 'ใช่' : 'ไม่') . "\n";
    
    return $allDelivered;
});

// 10. เช็คว่าสินค้าถูกส่งครบตามจำนวนหรือไม่
printSection("10. ตรวจสอบการจัดส่งสินค้า");
testFunctionality("ตรวจสอบจำนวนสินค้าที่จัดส่ง", function() use ($order, $component) {
    $items = $order->items;
    $deliveredQtyMap = $component->deliveredQtyMap;
    
    echo "รายการสินค้าและจำนวนที่จัดส่งแล้ว:\n";
    
    foreach ($items as $item) {
        $ordered = $item->quantity;
        $delivered = $deliveredQtyMap[$item->id] ?? 0;
        $remaining = $ordered - $delivered;
        
        echo " - {$item->product_name}: สั่ง {$ordered} จัดส่งแล้ว {$delivered} คงเหลือ {$remaining}\n";
    }
    
    return true;
});

// 11. ตรวจสอบระบบการชำระเงิน
printSection("11. ตรวจสอบระบบการชำระเงิน");
testFunctionality("ตรวจสอบสถานะการชำระเงิน", function() use ($order, $component) {
    $totalAmount = $component->order_grand_total;
    $totalPaid = $order->payments->where('status', 'ชำระเงินแล้ว')->sum('amount');
    $totalPending = $order->payments->where('status', 'รอยืนยันยอด')->sum('amount');
    
    echo "ยอดรวมที่ต้องชำระ: " . number_format($totalAmount, 2) . " บาท\n";
    echo "ชำระเงินแล้ว: " . number_format($totalPaid, 2) . " บาท\n";
    echo "รอยืนยันยอด: " . number_format($totalPending, 2) . " บาท\n";
    echo "คงเหลือที่ต้องชำระ: " . number_format($totalAmount - $totalPaid, 2) . " บาท\n";
    
    echo "ประวัติการชำระเงิน:\n";
    foreach ($order->payments as $payment) {
        echo " - {$payment->created_at} | {$payment->amount} บาท | สถานะ: {$payment->status}\n";
    }
    
    return true;
});

// สรุปผลการทดสอบทั้งหมด
printSection("สรุปผลการทดสอบทั้งหมด");
echo "ทดสอบฟังก์ชันทั้งหมดในระบบ Order และ Delivery เสร็จสิ้น\n";
echo "กรุณาตรวจสอบผลลัพธ์ด้านบนเพื่อหา Error หรือข้อผิดพลาดในระบบ\n";

// จบการทดสอบ
echo "\n=================================================================\n";
echo "🏁 จบการทดสอบ | " . date('Y-m-d H:i:s') . "\n";
echo "=================================================================\n";

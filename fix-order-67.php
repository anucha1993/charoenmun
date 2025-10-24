<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING ORDER ID 67 ===\n";

try {
    DB::beginTransaction();
    
    // ดึงข้อมูล Order Items ของ Order ID 67
    $orderItems = \App\Models\Orders\OrderItemsModel::where('order_id', 67)->get();
    
    echo "Found {$orderItems->count()} items in Order 67:\n";
    
    foreach ($orderItems as $item) {
        echo "- Item ID: {$item->id}, Product ID: {$item->product_id}, Quantity: {$item->quantity}\n";
    }
    
    // หาและลบรายการที่มี quantity = 122
    $problemItem = \App\Models\Orders\OrderItemsModel::where('order_id', 67)
        ->where('product_id', 29)
        ->where('quantity', 122)
        ->first();
    
    if ($problemItem) {
        echo "\nDeleting problematic item (ID: {$problemItem->id}, Quantity: 122)...\n";
        $problemItem->delete();
        echo "✅ Deleted successfully!\n";
        
        // ตรวจสอบผลลัพธ์
        $remainingItems = \App\Models\Orders\OrderItemsModel::where('order_id', 67)->get();
        echo "\nRemaining items after fix:\n";
        
        foreach ($remainingItems as $item) {
            echo "- Item ID: {$item->id}, Product ID: {$item->product_id}, Quantity: {$item->quantity}\n";
        }
        
        // คำนวณยอดรวมใหม่
        $newTotal = $remainingItems->sum('total');
        
        // อัพเดต Order total
        $order = \App\Models\Orders\OrderModel::find(67);
        $oldTotal = $order->order_grand_total;
        
        echo "\nUpdating order totals...\n";
        echo "Old total: {$oldTotal}\n";
        echo "New total: {$newTotal}\n";
        
        // อัพเดตยอดรวมใน Order (ถ้ามี logic การคำนวณ VAT และส่วนลด)
        $order->order_subtotal = $newTotal;
        $order->order_grand_total = $newTotal; // สมมติไม่มี VAT หรือส่วนลด
        $order->save();
        
        echo "✅ Order totals updated!\n";
        
    } else {
        echo "\nNo problematic item found.\n";
    }
    
    DB::commit();
    echo "\n🎉 Fix completed successfully!\n";
    
} catch (\Exception $e) {
    DB::rollback();
    echo "\n❌ Error: {$e->getMessage()}\n";
}
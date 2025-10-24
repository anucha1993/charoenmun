<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// ตรวจสอบ Order ID 67
echo "=== ORDER ID 67 DETAILS ===\n";
$order = \App\Models\Orders\OrderModel::with('items')->find(67);

if ($order) {
    echo "Order #{$order->order_number}:\n";
    echo "Customer: {$order->customer->customer_name}\n\n";
    
    echo "=== ORDER ITEMS ===\n";
    foreach ($order->items as $item) {
        echo "- Product ID: {$item->product_id}\n";
        echo "  Name: {$item->product_name}\n";
        echo "  Quantity: {$item->quantity}\n";
        echo "  Product Calculation: {$item->product_calculation}\n";
        echo "  Unit Price: {$item->unit_price}\n";
        echo "  Total: {$item->total}\n";
        echo "  ---\n";
    }
    
    // ตรวจสอบการจัดส่งที่มีอยู่
    echo "\n=== DELIVERIES ===\n";
    $deliveries = \App\Models\Orders\OrderDeliverysModel::where('order_id', 67)->with('deliveryItems')->get();
    
    if ($deliveries->count() > 0) {
        foreach ($deliveries as $delivery) {
            echo "Delivery #{$delivery->order_delivery_number}:\n";
            foreach ($delivery->deliveryItems as $deliveryItem) {
                echo "  - Product ID: {$deliveryItem->orderItem->product_id}\n";
                echo "    Quantity Delivered: {$deliveryItem->quantity}\n";
            }
            echo "\n";
        }
    } else {
        echo "No deliveries found for this order.\n";
    }
    
    // คำนวณ stock ที่เหลือ
    echo "\n=== STOCK CALCULATION ===\n";
    foreach ($order->items as $item) {
        $delivered = \App\Models\Orders\OrderDeliveryItems::query()
            ->join('order_items', 'order_delivery_items.order_item_id', '=', 'order_items.id')
            ->where('order_items.order_id', 67)
            ->where('order_items.product_id', $item->product_id)
            ->sum('order_delivery_items.quantity');
            
        $remaining = $item->quantity - $delivered;
        echo "Product {$item->product_id}: Ordered {$item->quantity}, Delivered {$delivered}, Remaining {$remaining}\n";
    }
    
} else {
    echo "Order ID 67 not found\n";
}
<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\Orders\OrderItemsModel;

echo "Getting order items...\n";

$items = OrderItemsModel::select('product_name', 'product_type', 'product_unit', 'product_length')
    ->take(20)
    ->get();

echo "Found " . $items->count() . " items:\n";
echo str_repeat('-', 100) . "\n";
echo sprintf("%-50s | %-15s | %-10s | %-10s\n", 'Product Name', 'Type', 'Unit', 'Length');
echo str_repeat('-', 100) . "\n";

foreach($items as $item) {
    echo sprintf("%-50s | %-15s | %-10s | %-10s\n", 
        $item->product_name, 
        $item->product_type, 
        $item->product_unit, 
        $item->product_length
    );
}

// Look specifically for service items
echo "\n" . str_repeat('=', 100) . "\n";
echo "Looking for service items...\n";
echo str_repeat('=', 100) . "\n";

$serviceItems = OrderItemsModel::where(function($query) {
    $query->where('product_name', 'LIKE', '%บริการ%')
          ->orWhere('product_name', 'LIKE', '%service%')
          ->orWhere('product_name', 'LIKE', '%ขนส่ง%')
          ->orWhere('product_unit', 'LIKE', '%บริการ%');
})->select('product_name', 'product_type', 'product_unit', 'product_length')->get();

echo "Found " . $serviceItems->count() . " service items:\n";
foreach($serviceItems as $item) {
    echo sprintf("%-50s | %-15s | %-10s | %-10s\n", 
        $item->product_name, 
        $item->product_type, 
        $item->product_unit, 
        $item->product_length
    );
}
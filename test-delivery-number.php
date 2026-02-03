<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Orders\OrderDeliverysModel;
use App\Models\Orders\OrderModel;

// Check order 771
$order = OrderModel::find(771);
echo "Order ID: " . $order->id . "\n";
echo "Order Number: " . $order->order_number . "\n";

$prefix = $order->order_number . '-';
echo "Prefix: " . $prefix . "\n";

$maxDeliveryNumber = OrderDeliverysModel::where('order_delivery_number', 'like', $prefix . '%')
    ->orderByRaw("CAST(SUBSTRING(order_delivery_number, ?) AS UNSIGNED) DESC", [strlen($prefix) + 1])
    ->value('order_delivery_number');

echo "Max delivery number: " . $maxDeliveryNumber . "\n";

if ($maxDeliveryNumber) {
    $lastPart = substr($maxDeliveryNumber, strrpos($maxDeliveryNumber, '-') + 1);
    $running = intval($lastPart) + 1;
} else {
    $running = 1;
}

echo "Last part: " . ($lastPart ?? 'N/A') . "\n";
echo "Next running: " . $running . "\n";
echo "Next delivery number: " . sprintf('%s%03d', $prefix, $running) . "\n";

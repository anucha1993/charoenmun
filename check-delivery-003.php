<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Check OR26010212-003 ===\n\n";

// 1. Check if -003 exists
$found = DB::table('order_deliveries')
    ->where('order_delivery_number', 'OR26010212-003')
    ->first();

if ($found) {
    echo "OR26010212-003 EXISTS in DB (id={$found->id})\n";
} else {
    echo "OR26010212-003 does NOT exist in DB - was DELETED\n";
}

// 2. Check ID gap
echo "\n--- All deliveries for OR26010212 ---\n";
$deliveries = DB::table('order_deliveries')
    ->where('order_delivery_number', 'like', 'OR26010212-%')
    ->orderBy('id')
    ->get(['id', 'order_delivery_number', 'created_at']);

foreach ($deliveries as $d) {
    echo "  ID={$d->id}  {$d->order_delivery_number}  created={$d->created_at}\n";
}

// 3. Check ID 1142 (missing from sequence)
echo "\n--- Check missing ID 1142 ---\n";
$id1142 = DB::table('order_deliveries')->where('id', 1142)->first();
echo $id1142 ? "ID 1142 exists: {$id1142->order_delivery_number}" : "ID 1142 does NOT exist (deleted)";
echo "\n";

// 4. Check Laravel logs for -003 creation
echo "\n--- Check log for OR26010212-003 ---\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $found003 = false;
    foreach ($lines as $line) {
        if (str_contains($line, 'OR26010212-003') || str_contains($line, 'OR26010212')) {
            echo "  " . trim($line) . "\n";
            $found003 = true;
        }
    }
    if (!$found003) {
        echo "  No log entries found for OR26010212 (log may have been rotated)\n";
    }
} else {
    echo "  Log file not found\n";
}

// 5. Check auto_increment
$status = DB::select("SHOW TABLE STATUS WHERE `Name` = 'order_deliveries'");
echo "\n--- Table auto_increment: {$status[0]->Auto_increment} ---\n";

<?php

require_once 'vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

try {
    echo "Testing Endroid QR Code generation...\n";
    
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data('Test QR Code - Delivery ID: 123')
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::Low)
        ->size(80)
        ->margin(2)
        ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->build();
    
    $qrCodeData = $result->getString();
    
    echo "QR Code generated successfully!\n";
    echo "Data length: " . strlen($qrCodeData) . " bytes\n";
    echo "Base64 preview: " . substr(base64_encode($qrCodeData), 0, 50) . "...\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
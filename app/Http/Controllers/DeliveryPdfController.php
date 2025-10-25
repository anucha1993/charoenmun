<?php

namespace App\Http\Controllers;

use App\Models\Orders\OrderDeliverysModel;
use App\Services\DeliveryPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryPdfController extends Controller
{
    public function generatePdf(OrderDeliverysModel $delivery, Request $request)
    {
        try {
            // Clear any previous output
            if (ob_get_length()) {
                ob_clean();
            }
            
            $pdfService = new DeliveryPdfService($delivery);
            $pdf = $pdfService->generatePDF();
            
            $filename = 'delivery-' . $delivery->order_delivery_number . '.pdf';
            
            // ตรวจสอบว่าเป็นการ preview หรือ download
            $isPreview = $request->has('preview');
            
            if ($isPreview) {
                // แสดง PDF ในเบราว์เซอร์ (preview)
                return response($pdf->Output('', 'S'), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                    ->header('Cache-Control', 'no-cache');
            } else {
                // ดาวน์โหลด PDF
                return response($pdf->Output('', 'S'), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->header('Cache-Control', 'no-cache');
            }
            
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'เกิดข้อผิดพลาดในการสร้าง PDF',
                'message' => $e->getMessage(),
                'delivery_id' => $delivery->id,
                'delivery_number' => $delivery->order_delivery_number
            ], 500);
        }
    }
}
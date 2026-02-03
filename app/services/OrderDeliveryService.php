<?php

namespace App\Services;

use App\Models\Orders\OrderModel;
use App\Models\Orders\OrderDeliverysModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class OrderDeliveryService
{

    public function storeDelivery(OrderModel $order, array $payload): OrderDeliverysModel
    {
        return DB::transaction(function () use ($order, $payload) {

            /* หา running number จากเลขสูงสุดที่มีอยู่ (ค้นหาทั้ง table เพราะ delivery_number unique globally) */
            $prefix = $order->order_number . '-';
            $maxDeliveryNumber = OrderDeliverysModel::where('order_delivery_number', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByRaw("CAST(SUBSTRING(order_delivery_number, ?) AS UNSIGNED) DESC", [strlen($prefix) + 1])
                ->value('order_delivery_number');
            
            if ($maxDeliveryNumber) {
                // ดึงเลขท้ายหลังขีด เช่น "OR26010212-004" -> "004" -> 4
                $lastPart = substr($maxDeliveryNumber, strrpos($maxDeliveryNumber, '-') + 1);
                $running = intval($lastPart) + 1;
            } else {
                $running = 1;
            }
            
            $deliveryNo = sprintf('%s-%03d', $order->order_number, $running);

            /* รวม field ที่อนุญาต */
            $data = Arr::only($payload, [
                'order_delivery_date', 'order_delivery_status', 'payment_status','delivery_address_id',
                'order_delivery_note', 'order_delivery_subtotal',
                'order_delivery_vat', 'order_delivery_discount',
                'order_delivery_grand_total', 'order_delivery_enable_vat',
                'order_delivery_vat_included',
            ]) + [
                'order_id'             => $order->id,
                'order_delivery_number'=> $deliveryNo,
                'created_by'           => auth()->id(),
            ];

            /* สร้างบิล */
            $delivery = OrderDeliverysModel::create($data);

            /* ถ้ามี items ใน $payload['items'] ให้บันทึกด้วย */
            if (!empty($payload['items'])) {
                $delivery->deliveryItems()->createMany($payload['items']);
            }

            return $delivery;
        });
    }

    /* ── 2) อัปเดตบิลจัดส่ง ────────────────────── */
    public function updateDelivery(OrderDeliverysModel $delivery, array $payload): OrderDeliverysModel
    {
        return DB::transaction(function () use ($delivery, $payload) {

            $data = Arr::only($payload, [
                'order_delivery_date', 'order_delivery_status', 'payment_status','delivery_address_id',
                'order_delivery_note', 'order_delivery_subtotal',
                'order_delivery_vat', 'order_delivery_discount',
                'order_delivery_grand_total', 'order_delivery_enable_vat',
                'order_delivery_vat_included',
            ]) + [
                'updated_by' => auth()->id(),
            ];

            $delivery->update($data);

            /* กรณีต้อง sync รายการสินค้าใหม่ทั้งหมด */
            if (isset($payload['items'])) {
                $delivery->deliveryItems()->delete();
                $delivery->deliveryItems()->createMany($payload['items']);
            }

            return $delivery;
        });
    }
}

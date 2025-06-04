<?php

namespace App\Livewire\Orders;

use App\Models\Orders\OrderDeliveryItems;
use Livewire\Component;
use App\Models\Orders\OrderModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Orders\OrderDeliverysModel;

class OrderShow extends Component
{
   public OrderModel $order;

    public function mount(OrderModel $order)
    {
        $this->order = $order->load(['customer', 'deliveryAddress', 'items', 'deliveries.deliveryItems']);
    }

    public function render()
    {
        return view('livewire.orders.order-show')->layout('layouts.horizontal', ['title' => 'Order #' . $this->order->order_number]);;
    }

    /**
     * กดปุ่มสร้างรอบจัดส่งใหม่ (Delivery) 
     * → clone ข้อมูลจาก order_items
     */

      public function createNewDelivery()
    {
        // เมื่อผู้ใช้กดปุ่ม “สร้างรอบจัดส่งใหม่” → redirect ไปหน้าฟอร์มสร้างรอบจัดส่ง
        return redirect()->route('order-delivery.create', $this->order->id);
    }
    
    // public function createNewDelivery()
    // {
    //     // ตรวจยอดคงเหลือก่อน: ถ้า order.status != 'open' ให้หยุด
    //     if ($this->order->order_status !== 'open') {
    //         $this->dispatchBrowserEvent('notify', [
    //             'type' => 'error',
    //             'message' => 'ไม่สามารถสร้างรอบจัดส่งใหม่ได้ เนื่องจากสถานะออร์เดอร์ไม่เปิด'
    //         ]);
    //         return;
    //     }

    //     DB::transaction(function () {
    //         // 1) generate order_delivery_number (เช่น ODL24060001)
    //         $deliveryNumber = $this->generateOrderDeliveryNumber();

    //         // 2) สร้าง order_delivery (header)
    //         $delivery = OrderDeliverysModel::create([
    //             'order_id'               => $this->order->id,
    //             'order_delivery_number'  => $deliveryNumber,
    //             'delivery_date'          => now()->toDateString(),
    //             'delivery_status'        => 'pending',
    //             'payment_status'         => 'pending',
    //             'note'                   => null,
    //             'created_by'             => Auth::id(),
    //             'updated_by'             => Auth::id(),
    //         ]);

    //         // 3) วนสร้าง order_delivery_items จาก order_items ทั้งหมด
    //         foreach ($this->order->items as $orderItem) {
    //             // ถ้ายังมียอดคงเหลือ (quantity > 0) ให้สร้างเป็น delivery item
    //             if ($orderItem->quantity > 0) {
    //                 OrderDeliveryItems::create([
    //                     'order_delivery_id' => $delivery->id,
    //                     'order_item_id'     => $orderItem->id,
    //                     // สมมติคราวนี้ส่งเต็มที่
    //                     'quantity'          => $orderItem->quantity,
    //                     'unit_price'        => $orderItem->unit_price,
    //                     'total'             => $orderItem->quantity * $orderItem->unit_price,
    //                 ]);

    //                 // 4) ลดยอดคงเหลือใน order_items (เหลือ = 0)
    //                 $orderItem->update([
    //                     'quantity' => 0,
    //                 ]);
    //             }
    //         }

    //         // 5) อัปเดตสถานะออร์เดอร์หลักถ้าไม่มีสินค้าเหลือ
    //         $remaining = $this->order->items()->sum('quantity');
    //         if ($remaining == 0) {
    //             $this->order->update([
    //                 'status' => 'completed',
    //             ]);
    //         }

    //         $this->order->load('deliveries.deliveryItems', 'items');

    //         $this->dispatchBrowserEvent('notify', [
    //             'type' => 'success',
    //             'message' => 'สร้างรอบจัดส่ง #' . $deliveryNumber . ' เรียบร้อย'
    //         ]);
    //     });
    // }

    /**
     * ตัวอย่างฟังก์ชันสุ่ม Order Delivery Number
     */
    private function generateOrderDeliveryNumber(): string
    {
        $prefix = 'ODL' . now()->format('ym');
        $last = OrderDeliverysModel::where('order_delivery_number', 'like', $prefix . '%')
                             ->orderBy('order_delivery_number', 'desc')
                             ->value('order_delivery_number');

        $next = $last
            ? ((int) substr($last, -4)) + 1
            : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * กดปุ่มยืนยันจัดส่งในรอบนี้ → delivery_status = 'delivered'
     */
    public function markDeliveryAsDelivered($deliveryId)
    {
        $delivery = OrderDeliverysModel::findOrFail($deliveryId);

        if ($delivery->delivery_status !== 'pending') {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'รอบจัดส่งนี้ไม่สามารถเปลี่ยนสถานะได้'
            ]);
            return;
        }

        $delivery->update([
            'delivery_status' => 'delivered',
            'updated_by'      => Auth::id(),
        ]);

        $this->order->load('deliveries.deliveryItems');

        $this->dispatchBrowserEvent('notify', [
            'type' => 'success',
            'message' => 'ยืนยันว่ารอบจัดส่ง #' . $delivery->order_delivery_number . ' จัดส่งเรียบร้อย'
        ]);

        // ถ้าออร์เดอร์หลักไม่มี order_items เหลือแล้ว สถานะจะเปลี่ยนเป็น completed (ทำไว้ใน createNewDelivery)
    }
}
